<?php
namespace CentreonModule\Infrastructure\Service;

use Psr\Container\ContainerInterface;
use CentreonModule\Infrastructure\Source;

class CentreonModuleService
{

    /**
     * Construct
     * 
     * @param \Psr\Container\ContainerInterface $services
     */
    public function __construct(ContainerInterface $services)
    {
        $this->db = $services->get('centreon.db-manager');
        $this->finder = $services->get('finder');

        $this->sources = [
            Source\ModuleSource::TYPE => new Source\ModuleSource($services),
            Source\WidgetSource::TYPE => new Source\WidgetSource($services),
        ];
    }

    public function getList(string $search = null, bool $installed = null, bool $updated = null, array $typeList = null)
    {
        $result = [];
        
        if ($typeList !== null && $typeList) {
            $sources = [];

            foreach ($this->sources as $type => $source) {
                if (!in_array($type, $typeList)) {
                    continue;
                }

                $sources[$type] = $source;
            }
        } else {
            $sources = $this->sources;
        }

        foreach ($sources as $type => $source) {
            $result[$type] = $source->getList($search, $installed, $updated);
        }

        return $result;
    }
}
