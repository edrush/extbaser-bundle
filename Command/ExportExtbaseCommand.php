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
 * Export an existing database scheme to a Extbase project file (ExtensionBuilder.json).
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
            ->setDescription('Export an existing database scheme to a TYPO3 Extbase extension project.')
            
            ->addArgument('extension-key', InputArgument::REQUIRED, 'The target TYPO3 extension key.')

            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path to export the extension to.')
            ->addOption('filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A string pattern used to match entities that should be mapped.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Roundtrip existing project.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$extensionKey = $input->getArgument('extension-key');
        $exportPath = $input->getOption('path') ? $input->getOption('path') : $this->getContainer()->getParameter("kernel.cache_dir");

        $em = $this->getContainer()->get('doctrine')->getManager($input->getOption('em'));

        $emName = $input->getOption('em');
        $emName = $emName ? $emName : 'default';

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);
        
        $exporter = new ExtbaseExporter($cmf);
        $exporter->setExtensionKey($extensionKey);
        $exporter->setPath($exportPath);
        $exporter->setOverwriteExistingFiles($input->getOption('force'));
        $exporter->setFilter($input->getOption('filter'));
        
        $output->writeln(sprintf('Importing mapping information from "<info>%s</info>" entity manager', $emName));
        
        $result = $exporter->exportJson();
        
        foreach ($exporter->getLogs() as $log) {
        	$output->writeln($log);
        }
        
        return $result? 0 : 1;
    }
}
