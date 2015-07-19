<?php

namespace EdRush\Bundle\ExtbaserBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use EdRush\Extbaser\ExtbaseExporter;

/**
 * Export an existing database schema to a Extbase project file (ExtensionBuilder.json).
 *
 * @author Wolfram Eberius <edrush@posteo.de>
 */
class ExportExtbaseCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('extbaser:export')
            ->setDescription('Export an existing Symfony database schema to a TYPO3 Extbase Extension')

            ->addArgument('extension-key', InputArgument::REQUIRED, 'The target TYPO3 Extension key')

            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path to export the extension to', $this->getContainer()->getParameter('kernel.cache_dir'))
            ->addOption('em', null, InputOption::VALUE_OPTIONAL)
        ;

        foreach (\EdRush\Extbaser\Command\ExportExtbaseCommand::getDefaultInputOptions() as $inputOption) {
            $this->getDefinition()->addOption($inputOption);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager($input->getOption('em'));

        $emName = $input->getOption('em');
        $emName = $emName ? $emName : 'default';

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);

        $exporter = new ExtbaseExporter($cmf);
        $exporter->setExtensionKey($input->getArgument('extension-key'));
        $exporter->setPath($input->getOption('path'));
        \EdRush\Extbaser\Command\ExportExtbaseCommand::mapDefaultInputOptions($exporter, $input);

        $output->writeln(sprintf('Importing mapping information from "<info>%s</info>" entity manager', $emName));

        $result = $exporter->exportJson();

        foreach ($exporter->getLogs() as $log) {
            $output->writeln($log);
        }

        return $result ? 0 : 1;
    }
}
