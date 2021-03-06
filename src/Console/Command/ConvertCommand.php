<?php
namespace Pickle\Console\Command;

use Pickle\Package\JSON\Dumper;
use Pickle\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDescription('Convert package.xml to new format')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path to the PECL extension root directory (default pwd)',
                getcwd()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = rtrim($input->getArgument('path'), '/\\');

        if (is_file($path . DIRECTORY_SEPARATOR . 'package.xml') === false) {
            throw new \InvalidArgumentException('File not found: ' . $path . DIRECTORY_SEPARATOR . 'package.xml');
        }

        $loader = new Package\XML\Loader(new Package\Loader());
        $package = $loader->load($path . DIRECTORY_SEPARATOR . 'package.xml');

        $dumper = new Dumper();
        $dumper->dumpToFile($package, $path . DIRECTORY_SEPARATOR . 'pickle.json');

        $output->writeln('<info>Successfully converted ' . $package->getPrettyName() . '</info>');

        $this->getHelper('package')->showInfo($output, $package);
    }
}
