<?php

namespace common\models;

trait VacationPackagesTrait
{
    /**
     * Get vacation packages
     *
     * @return VacationPackageOrder[]
     */
    public function getVacationPackages(): array
    {
        $data = $this->getData();
        $packages = [];

        if (!empty($data['vacationPackages'])) {
            foreach ($data['vacationPackages'] as $packageData) {
                $package = new VacationPackageOrder;
                $package->loadData($packageData);
                $packages[] = $package;
            }
        }

        return $packages;
    }

    /**
     * Get group vacation packages
     *
     * @return array
     */
    public function getGroupVacationPackages(): array
    {
        $uniques = [];
        foreach ($this->getVacationPackages() as $vacationPackage) {
            $keys = [];
            foreach ($vacationPackage->getPackages() as $package) {
                $key = implode(
                    '.',
                    [
                        $vacationPackage->config_id,
                        $package->id,
                        $package->category,
                        $package->getStartDataTime()->format('Y-m-d_H:i:s')
                    ]
                );
                $keys[] = $key;
            }
            arsort($keys);
            $uniques[md5(implode('.', $keys))][] = $vacationPackage;
        }
        return $uniques;
    }

    /**
     * Get unique vacation packages
     *
     * @return VacationPackageOrder[]
     */
    public function getUniqueVacationPackages(): array
    {
        $uniques = $this->getGroupVacationPackages();
        foreach ($uniques as $key => $ar) {
            $ar[0]->count = count($ar);
            $uniques[$key] = $ar[0];
        }
        return $uniques;
    }

    /**
     * Get valid unique vacation packages
     *
     * @return VacationPackageOrder[]
     */
    public function getValidUniqueVacationPackages(): array
    {
        $result = [];
        foreach ($this->getUniqueVacationPackages() as $vacationPackage) {
            if (!$vacationPackage->cancelled) {
                $result[] = $vacationPackage;
            }
        }
        return $result;
    }

    /**
     * Get valid vacation packages
     *
     * @return VacationPackageOrder[]
     */
    public function getValidVacationPackages(): array
    {
        $result = [];
        foreach ($this->getVacationPackages() as $vacationPackage) {
            if (!$vacationPackage->cancelled) {
                $result[] = $vacationPackage;
            }
        }
        return $result;
    }

    /**
     * @param int $id
     *
     * @return null|VacationPackageOrder
     */
    public function getVacationPackage($id): ?VacationPackageOrder
    {
        $packages = $this->getVacationPackages();

        if (!empty($packages)) {
            foreach ($packages as $package) {
                if ($package->id == $id) {
                    return $package;
                }
            }
        }

        return null;
    }

    /**
     * @param string $packageId
     *
     * @return null|Package
     */
    public function getPackageFromVPByPackageId($packageId): ?Package
    {
        $vPackages = $this->getVacationPackages();
        if (!empty($vPackages)) {
            foreach ($vPackages as $vPackage) {
                $packages = $vPackage->getPackages();
                if (!empty($packages)) {
                    foreach ($packages as $package) {
                        if ($package->package_id == $packageId) {
                            return $package;
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get unique group vacation packages hash
     *
     * @param int $id
     *
     * @return null|string
     */
    public function getGroupHashVacationPackageById($id): ?string
    {
        foreach ($this->getGroupVacationPackages() as $uniqueHash => $VacationPackages) {
            foreach ($VacationPackages as $VacationPackage) {
                if ($VacationPackage->id == $id) {
                    return $uniqueHash;
                }
            }
        }
        return null;
    }

    /**
     * Get unique vacation packages by id
     *
     * @param int $id
     *
     * @return null|VacationPackageOrder
     */
    public function getUniqueVacationPackageById($id): ?VacationPackageOrder
    {
        if ($hash = $this->getGroupHashVacationPackageById($id)) {
            if (!empty($this->getUniqueVacationPackages()[$hash])) {
                return $this->getUniqueVacationPackages()[$hash];
            }
        }
        return null;
    }
}
