<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\ViewModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Cloudflex\LogsManagement\Helper\TreeBuilder;

class LogsTree implements ArgumentInterface
{
    /**
     * @param DirectoryList $directoryList
     * @param File $fileIo
     * @param TreeBuilder $treeBuilder
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly DirectoryList $directoryList,
        private readonly File $fileIo,
        private readonly TreeBuilder $treeBuilder,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Get the serialized tree of catalog and log files
     *
     * @return string
     */
    public function getTree(): string
    {
        $tree = [];
        try {
            $tree = $this->treeBuilder->buildTree($this->getItems(), TreeBuilder::ROOT_ID);
        } catch (LocalizedException|FileSystemException $e) {
            return $this->serializer->serialize($tree);
        }

        return $this->serializer->serialize($tree);
    }

    /**
     * Get the items from the log directory
     *
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function getItems(): array
    {
        $logDir = $this->directoryList->getPath(DirectoryList::LOG);
        $this->fileIo->cd($logDir);

        return $this->fileIo->ls(3);
    }
}
