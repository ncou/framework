<?php
/**
 * @author    aelix framework <info@aelix.org>
 * @copyright Copyright (c) 2015 aelix framework
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 */
declare(strict_types = 1);

namespace aelix\framework\navigation;


use aelix\framework\template\ITemplatable;

class Navigation implements ITemplatable
{
    /**
     * @var NavigationEntry[] Holds all entries of this nav. navKey => entry
     */
    protected $entries = [];

    /**
     * @var string|null navKey of the active entry. Null if nothing is active
     */
    protected $activeKey = null;

    /**
     * @param string $key nav key of the new entry (internal)
     * @param string $name Name of the nav entry to be printed
     * @param string $url URL of the hyperlink
     * @param bool $active is this new entry the active one?
     * @return $this
     */
    public function addEntry(string $key, string $name, string $url, bool $active = false)
    {
        $this->entries[$key] = new NavigationEntry($key, $name, $url, $active);
        if ($active) {
            $this->setActive($key);
        }

        return $this;
    }

    /**
     * @param string $key nav key
     * @return Navigation
     * @throws NavigationEntryDoesntExistException
     */
    public function setActive(string $key): self
    {
        if (!isset($this->entries[$key])) {
            throw new NavigationEntryDoesntExistException();
        }

        if ($this->activeKey !== null) {
            $this->entries[$this->activeKey]->setActive(false);
        }
        $this->entries[$key]->setActive();
        $this->activeKey = $key;

        return $this;
    }

    /**
     * Removes a entry from this navigation. If entry is active, it will be removed and no new entry will be active.
     * @param string $key string key of the entry to remove
     * @return Navigation
     * @throws NavigationEntryDoesntExistException
     */
    public function removeEntry(string $key): self
    {
        if (!isset($this->entries[$key])) {
            throw new NavigationEntryDoesntExistException();
        }

        if ($this->activeKey == $key) {
            $this->setNothingActive();
        }

        unset($this->entries[$key]);

        return $this;
    }

    /**
     * no entry will be active
     * @return Navigation
     */
    public function setNothingActive(): self
    {
        if ($this->activeKey !== null) {
            $this->entries[$this->activeKey]->setActive(false);
            $this->activeKey = null;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateArray(): array
    {
        $entries = [];

        foreach ($this->entries as $entry) {
            $entries[$entry->getKey()] = $entry->getTemplateArray();
        }

        return [
            'activeKey' => $this->activeKey,
            'entries' => $entries,
        ];
    }

    /**
     * @return NavigationEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * Get the currently active entry
     * @return NavigationEntry
     */
    public function getActiveEntry(): NavigationEntry
    {
        if ($this->activeKey === null) {
            return null;
        }
        return $this->entries[$this->activeKey];
    }
}