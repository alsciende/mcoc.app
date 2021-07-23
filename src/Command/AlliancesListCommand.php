<?php

namespace App\Command;

use App\Entity\Alliance;
use App\Repository\AllianceRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlliancesListCommand extends Command
{
    protected static $defaultName = 'app:alliances:list';

    /**
     * @var AllianceRepository
     */
    private AllianceRepository $repository;

    /**
     * AlliancesListCommand constructor.
     * @param AllianceRepository $repository
     */
    public function __construct(AllianceRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Display a list of all alliances')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->table(['Id', 'Tag', 'Name'], array_map(function (Alliance $alliance) {
            return [ $alliance->getId(), $alliance->getTag(), $alliance->getName() ];
        }, $this->repository->findAll()));

        $io->success('Done');

        return Command::SUCCESS;
    }
}
