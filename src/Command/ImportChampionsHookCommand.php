<?php

namespace App\Command;

use App\Entity\Champion;
use App\Entity\Character;
use App\Entity\ExternalCharacter;
use App\Import\Hook\ChampionDataExtractor;
use App\Repository\ChampionRepository;
use App\Repository\ExternalCharacterRepository;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportChampionsHookCommand extends Command
{
    protected static $defaultName = 'app:import:champions:hook';

    /**
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $containerBag;

    /**
     * @var ChampionDataExtractor
     */
    private ChampionDataExtractor $championDataExtractor;

    /**
     * @var ExternalCharacterRepository
     */
    private ExternalCharacterRepository $externalCharacterRepository;

    /**
     * @var CharacterRepository
     */
    private CharacterRepository $characterRepository;

    /**
     * @var ChampionRepository
     */
    private ChampionRepository $championRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * ImportChampionsHookCommand constructor.
     */
    public function __construct(
        ContainerBagInterface $containerBag,
        ChampionDataExtractor $championDataExtractor,
        ExternalCharacterRepository $externalCharacterRepository,
        CharacterRepository $characterRepository,
        ChampionRepository $championRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ) {
        parent::__construct();
        $this->containerBag = $containerBag;
        $this->championDataExtractor = $championDataExtractor;
        $this->externalCharacterRepository = $externalCharacterRepository;
        $this->characterRepository = $characterRepository;
        $this->championRepository = $championRepository;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Import Champions data from https://github.com/hook/champions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Champions import from Hook's Github");

        $io->section("Checkout Github");
        $cacheDir = $this->containerBag->get('kernel.cache_dir');
        $publicDir = $this->containerBag->get('kernel.project_dir') . '/public';

        $this->checkout($cacheDir);

        $io->section("Read Data");
        $idsData = $this->championDataExtractor->extractIdData($this->readIdsChampionData($cacheDir));

        $championData = $this->championDataExtractor->extractChampionData($this->readChampionsFile($cacheDir));

        $localeData = json_decode($this->readLocalisationFile($cacheDir, 'en'), true);

        $fs = new Filesystem();

        $io->section("Copy Portraits and Update Database");
        foreach ($championData as $type => $championDatum) {
            foreach ($championDatum as $constant => $stars) {
                $id = $idsData[$constant];
                $name = $localeData['champion-' . $id . '-name'];
                $imageTargetPath = $this->getPortraitImageTargetPath($name);
                $fs->copy(
                    $this->getPortraitImageSourcePath($cacheDir, $id),
                    $publicDir . $imageTargetPath
                );
                $this->syncChampion($id, $name, $stars, $type, $imageTargetPath);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $id
     * @param string $name
     * @param int[]  $stars
     * @param string $type
     */
    private function syncChampion(string $id, string $name, array $stars, string $type, string $imagePath): void
    {
        $found = $this->externalCharacterRepository->findBy([
            'source'     => ExternalCharacter::SOURCE_HOOK,
            'externalId' => $id,
        ]);

        if (count($found) > 0) {
            return;
        }

        $character = new Character();
        $character->setType($type);
        $character->setName($name);
        $character->setPicture($imagePath);
        $this->entityManager->persist($character);

        $ExternalCharacter = new ExternalCharacter();
        $ExternalCharacter->setCharacter($character);
        $ExternalCharacter->setSource(ExternalCharacter::SOURCE_HOOK);
        $ExternalCharacter->setExternalId($id);
        $this->entityManager->persist($ExternalCharacter);

        foreach ($stars as $star) {
            $champion = new Champion();
            $champion->setCharacter($character);
            $champion->setTier($star);
            $this->entityManager->persist($champion);
        }

        $this->entityManager->flush();
    }

    private function checkout(string $dir): void
    {
        if (false === is_dir($dir . '/champions')) {
            shell_exec('cd ' . $dir . ' && git clone https://github.com/hook/champions');
        }

        shell_exec('cd ' . $dir . '/champions && git checkout --force master');
    }

    private function readIdsChampionData(string $dir): string
    {
        $filename = $dir . '/champions/src/data/ids/champions.js';

        if (false === is_file($filename)) {
            throw new \RuntimeException('Missing file: ' . $filename);
        }

        return file_get_contents($filename);
    }

    private function readChampionsFile(string $dir): string
    {
        $filename = $dir . '/champions/src/data/champions.js';

        if (false === is_file($filename)) {
            throw new \RuntimeException('Missing file: ' . $filename);
        }

        return file_get_contents($filename);
    }

    private function readLocalisationFile(string $dir, string $locale): string
    {
        $filename = $dir . '/champions/src/data/lang/' . $locale . '.json';

        if (false === is_file($filename)) {
            throw new \RuntimeException('Missing file: ' . $filename);
        }

        return file_get_contents($filename);
    }

    private function getPortraitImageSourcePath(string $dir, string $id): string
    {
        return $dir . '/champions/src/images/champions/portrait_' . $id . '.png';
    }

    private function getPortraitImageTargetPath(string $name): string
    {
        $slug = strtolower($this->slugger->slug($name));

        return '/images/portraits/' . $slug . '.png';
    }
}
