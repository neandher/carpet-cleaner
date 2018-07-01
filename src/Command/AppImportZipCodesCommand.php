<?php

namespace App\Command;

use App\Entity\ZipCode;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppImportZipCodesCommand extends Command
{
    protected static $defaultName = 'app:import:zip-codes';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $zipCodePath;

    /**
     * AppImportZipCodesCommand constructor.
     * @param null $name
     * @param EntityManagerInterface $em
     * @param $zipCodePath
     */
    public function __construct($name = null, EntityManagerInterface $em, $zipCodePath)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->zipCodePath = $zipCodePath;
    }


    protected function configure()
    {
        $this
            ->setDescription('Import zip codes from excel file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $inputFileName = $this->zipCodePath;
        $io->writeln('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME));

        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $codes = [];
        foreach ($sheetData as $data) {
            if (!is_null($data['C']) && !in_array($data['C'], $codes)) {

                $codes[] = $data['C'];
                $zipCode = new ZipCode();
                $zipCode->setDescription($data['A']);

                $persist = false;

                if ($data['C'] < 34) {
                    $zipCode->setPercentage(0);
                    $persist = true;
                }
                if ($data['C'] > 33 && $data['C'] < 51) {
                    $zipCode->setPercentage(0.40);
                    $persist = true;
                }
                if ($persist)
                    $this->em->persist($zipCode);
            }
        }

        $this->em->flush();

        $io->success('Zip codes imported with success!');
    }
}
