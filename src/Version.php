<?php

namespace SteveGrunwell\SemVer;

use SteveGrunwell\SemVer\Exceptions\InvalidVersionException;

class Version
{
    /**
     * @var int The major version.
     */
    protected $major;

    /**
     * @var int The minor version.
     */
    protected $minor;

    /**
     * @var int The patch version.
     */
    protected $patch;

    /**
     * @var string The original version string that was provided.
     */
    protected $version;

    /**
     * Create the version.
     *
     * @return self
     */
    public function __construct(string $version = '')
    {
        $this->version = $version;
    }

    /**
     * If cast as a string, automatically call getVersion().
     */
    public function __toString(): string
    {
        return $this->getVersion();
    }

    /**
     * Retrieve the string-ified version.
     */
    public function getVersion(): string
    {
        return sprintf(
            '%1$d.%2$d.%3$d',
            $this->getMajorVersion(),
            $this->getMinorVersion(),
            $this->getPatchVersion()
        );
    }

    /**
     * Get the major version.
     */
    public function getMajorVersion(): int
    {
        return $this->parseVersion()->major;
    }

    /**
     * Get the minor version.
     */
    public function getMinorVersion(): int
    {
        return $this->parseVersion()->minor;
    }

    /**
     * Get the patch version.
     */
    public function getPatchVersion(): int
    {
        return $this->parseVersion()->patch;
    }

    /**
     * Set the major version.
     */
    public function setMajorVersion(int $value): self
    {
        return $this->setVersionDigit('major', $value);
    }

    /**
     * Set the minor version.
     */
    public function setMinorVersion(int $value): self
    {
        return $this->setVersionDigit('minor', $value);
    }

    /**
     * Set the patch version.
     */
    public function setPatchVersion(int $value): self
    {
        return $this->setVersionDigit('patch', $value);
    }

    /**
     * Set the given digit.
     *
     * @throws \SteveGrunwell\SemVer\Exceptions\InvalidVersionException If $value is < 0.
     *
     * @param string $digit   One of "major", "minor", or "patch".
     * @param int    $default The default value if one cannot be parsed.
     */
    protected function setVersionDigit(string $digit, int $value): self
    {
        if (0 > $value) {
            throw new InvalidVersionException('Digits must be non-negative integers.');
        }

        $this->parseVersion()->{$digit} = $value;

        return $this;
    }

    /**
     * Parse $this->version and populate the $major, $minor, and $patch properties.
     */
    protected function parseVersion(): self
    {
        // If these are all null, we have yet to parse.
        if (isset($this->major, $this->minor, $this->patch)) {
            return $this;
        }

        $values = explode('.', $this->version, 3);
        $values = array_map('intval', $values);

        // Ensure we have three entries, map them to major, minor, and patch.
        list($this->major, $this->minor, $this->patch) = array_pad($values, 3, 0);

        return $this;
    }
}
