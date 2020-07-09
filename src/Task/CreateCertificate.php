<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class CreateCertificate
{
    const CREATE_COMMAND_FORMAT = 'openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout ${HOME}/.genly/nginx/certs/%1$s.key -out ${HOME}/.genly/nginx/certs/%1$s.crt -config %2$s';

    const CONFIG_FORMAT = <<<'EOF'
[req]
distinguished_name = req_distinguished_name
x509_extensions = v3_req
prompt = no

[v3_req]
keyUsage = digitalSignature, keyEncipherment
extendedKeyUsage = serverAuth
subjectAltName = @alt_names

[req_distinguished_name]
CN = %1$s

[alt_names]
DNS.1 = %1$s
EOF;

    const DARWIN_VERIFY_COMMAND_FORMAT = 'security verify-cert -c %s';
    const DARWIN_TRUST_COMMAND_FORMAT = 'sudo security add-trusted-cert -d -k /Library/Keychains/System.keychain %s';

    const LINUX_CA_DIR = '/usr/local/share/ca-certificates';
    const LINUX_TRUST_COMMAND_FORMAT = 'sudo cp %s %s';
    const LINUX_UPDATE_CA_STORE_COMMAND = 'sudo update-ca-certificates';

    public function __invoke(AbstractCommand $context, string $hostname)
    {
        $fs = new Filesystem();

        $dir = $_SERVER['HOME'].'/.genly/nginx/certs';
        $crtName = $hostname.'.crt';
        $crtPath = $dir.'/'.$crtName;

        $fs->mkdir($dir, 0775);

        if (!$fs->exists($crtPath)) {
            $tmpfile = $fs->tempnam(sys_get_temp_dir(), 'genly_');
            $fs->dumpFile($tmpfile, sprintf(self::CONFIG_FORMAT, $hostname));

            Process::fromShellCommandline(sprintf(self::CREATE_COMMAND_FORMAT, $hostname, $tmpfile))->mustRun();
        }

        $context->output->writeln("<info>Attempting to add {$crtName} to your trusted certificate store.</info>");

        switch (strtolower(PHP_OS)) {
            case 'darwin':
                $process = Process::fromShellCommandline(sprintf(static::DARWIN_VERIFY_COMMAND_FORMAT, $crtPath));
                $process->run();
                if ($process->isSuccessful()) {
                    $context->output->writeln("<comment>{$crtName} is already trusted.</comment>");
                } else {
                    Process::fromShellCommandline(sprintf(static::DARWIN_TRUST_COMMAND_FORMAT, $crtPath))->mustRun();
                    $context->output->writeln("<comment>{$crtName} is now trusted.</comment>");
                }
                break;

            case 'linux':
                if (!$fs->exists(self::LINUX_CA_DIR.'/'.$crtName)) {
                    (Process::fromShellCommandline(sprintf(static::LINUX_TRUST_COMMAND_FORMAT, $crtPath, self::LINUX_CA_DIR)))->mustRun();
                }
                Process::fromShellCommandline(self::LINUX_UPDATE_CA_STORE_COMMAND)->mustRun();
                $context->output->writeln("<comment>Certificate store has been updated.</comment>");
                break;

            default:
                $context->output->writeln("<comment>Cert could not be added to your trusted certificate store automatically.</comment>");
                $context->output->writeln("<comment>Please add {$crtName} to your trusted certificate store manually.</comment>");
                break;
        }
    }
}
