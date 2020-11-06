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
                $character = $this->syncChampion($id, $name, $type, $stars);
                $fs->copy(
                    $this->getPortraitImageSourcePath($cacheDir, $id),
                    $this->getPortraitImageTargetPath($publicDir, $character->getId())
                );
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
    private function syncChampion(string $id, string $name, string $type, array $stars): Character
    {
        $character = $this->syncCharacter($id, $name, $type);

        foreach ($stars as $star) {
            $found = $this->championRepository->findOneBy(['character' => $character, 'tier' => $star]);
            if ($found instanceof Champion) {
                continue;
            }

            $champion = new Champion();
            $champion->setCharacter($character);
            $champion->setTier($star);
            $champion->setId($character->getId() . '-' . $star);
            $this->entityManager->persist($champion);
        }

        $this->entityManager->flush();

        return $character;
    }

    private function syncCharacter(string $id, string $name, string $type): Character
    {
        $found = $this->externalCharacterRepository->findOneBy([
            'source'     => ExternalCharacter::SOURCE_HOOK,
            'externalId' => $id,
        ]);

        if ($found instanceof ExternalCharacter) {
            return $found->getCharacter();
        }

        $character = new Character();
        $character->setId(strtolower($this->slugger->slug($name)));
        $character->setType($type);
        $character->setName($name);
        $this->entityManager->persist($character);

        $ExternalCharacter = new ExternalCharacter();
        $ExternalCharacter->setCharacter($character);
        $ExternalCharacter->setSource(ExternalCharacter::SOURCE_HOOK);
        $ExternalCharacter->setExternalId($id);
        $this->entityManager->persist($ExternalCharacter);

        $this->entityManager->flush();

        return $character;
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

    private function getPortraitImageTargetPath(string $dir, string $id): string
    {
        return $dir . '/images/portrait/' . $id . '.png';
    }
}
