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
     * @var string The pre-release version.
     */
    protected $preRelease;

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
        $version = sprintf(
            '%1$d.%2$d.%3$d',
            $this->getMajorVersion(),
            $this->getMinorVersion(),
            $this->getPatchVersion()
        );

        // Append the pre-release, if available.
        if ($this->preRelease) {
            $version .= '-' . $this->preRelease;
        }

        return $version;
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
     * Get the pre-release version.
     */
    public function getPreReleaseVersion(): string
    {
        return $this->parseVersion()->preRelease;
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
     * Set the patch version.
     */
    public function setPreReleaseVersion(string $value): self
    {
        $this->parseVersion()->preRelease = $this->validateIdentifier($value);

        return $this;
    }

    /**
     * Increment the major version by one.
     */
    public function incrementMajorVersion(): self
    {
        $this->setVersionDigit('major', $this->getMajorVersion() + 1);
        $this->setVersionDigit('minor', 0);

        return $this->setVersionDigit('patch', 0);
    }

    /**
     * Increment the minor version by one.
     */
    public function incrementMinorVersion(): self
    {
        $this->setVersionDigit('minor', $this->getMinorVersion() + 1);

        return $this->setVersionDigit('patch', 0);
    }

    /**
     * Increment the patch version by one.
     */
    public function incrementPatchVersion(): self
    {
        return $this->setVersionDigit('patch', $this->getPatchVersion() + 1);
    }

    /**
     * Decrement the major version by one.
     */
    public function decrementMajorVersion(): self
    {
        return $this->setVersionDigit('major', $this->getMajorVersion() - 1);
    }

    /**
     * Decrement the minor version by one.
     */
    public function decrementMinorVersion(): self
    {
        return $this->setVersionDigit('minor', $this->getMinorVersion() - 1);
    }

    /**
     * Decrement the patch version by one.
     */
    public function decrementPatchVersion(): self
    {
        return $this->setVersionDigit('patch', $this->getPatchVersion() - 1);
    }

    /**
     * Set the given digit.
     *
     * @throws \SteveGrunwell\SemVer\Exceptions\InvalidVersionException If $value is < 0.
     *
     * @param string $digit One of "major", "minor", or "patch".
     * @param int    $value The value of digit.
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
        if (isset($this->major, $this->minor, $this->patch, $this->preRelease)) {
            return $this;
        }

        // If we have a pre-release, split that off.
        $multipleParts = explode('-', $this->version, 2);

        if (2 === count($multipleParts)) {
            list($version, $strings) = $multipleParts;
        } else {
            $version = $this->version;
        }

        $values = explode('.', $version, 3);
        $values = array_map('intval', $values);

        // Ensure we have three entries, map them to major, minor, and patch.
        list($this->major, $this->minor, $this->patch) = array_pad($values, 3, 0);

        // Handle pre-release versions, if available.
        $this->preRelease = ! empty($strings) ? $this->validateIdentifier($strings) : '';

        return $this;
    }

    /**
     * Validate permitted characters for pre-release versions.
     *
     * @link https://semver.org/spec/v2.0.0.html#spec-item-9
     *
     * @throws \SteveGrunwell\SemVer\Exceptions\InvalidVersionException If any illegal characters
     *         are found.
     */
    protected function validateIdentifier(string $identifier)
    {
        if (preg_match('/[^A-Za-z0-9-\.]/', $identifier)) {
            throw new InvalidVersionException('Identifiers may only contain ASCII alphanumerics, dots, and hyphens.');
        }

        return $identifier;
    }
}
