<?php

namespace App\Command;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CharactersListCommand extends Command
{
    protected static $defaultName = 'app:characters:list';

    /**
     * @var CharacterRepository
     */
    private CharacterRepository $repository;

    /**
     * CharactersListCommand constructor.
     * @param CharacterRepository $repository
     */
    public function __construct(CharacterRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Display a list of all characters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->table(['Id', 'Name', 'Type'], array_map(function (Character $character) {
            return [ $character->getId(), $character->getName(), $character->getType() ];
        }, $this->repository->findAll()));

        $io->success('Done');

        return Command::SUCCESS;
    }
}
