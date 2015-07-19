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
 * @author weberius
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
            ->setDescription('Convert mapping information to a TYPO3 Extbase Extension')

            ->addArgument('extension-key', InputArgument::REQUIRED, 'The target TYPO3 Extension key')

            ->addOption('path', null, InputOption::VALUE_OPTIONAL)
            ->addOption('em', null, InputOption::VALUE_OPTIONAL)
            ->addOption('from-database', null, null, 'Whether or not to convert mapping information from existing database')
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
        $path = $input->getOption('path') ? $input->getOption('path') : $this->getContainer()->getParameter('kernel.cache_dir');

        if ($input->getOption('from-database')) {
            $em->getConfiguration()->setMetadataDriverImpl(
                new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
                    $em->getConnection()->getSchemaManager()
                )
            );
        }

        $emName = $input->getOption('em');
        $emName = $emName ? $emName : 'default';

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);

        $exporter = new ExtbaseExporter($cmf);
        $exporter->setExtensionKey($input->getArgument('extension-key'));
        $exporter->setPath($path);
        \EdRush\Extbaser\Command\ExportExtbaseCommand::mapDefaultInputOptions($exporter, $input);

        $output->writeln(sprintf('Importing mapping information from "<info>%s</info>" entity manager', $emName));

        $result = $exporter->exportJson();

        foreach ($exporter->getLogs() as $log) {
            $output->writeln($log);
        }

        return $result ? 0 : 1;
    }
}
