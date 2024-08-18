<?php

declare(strict_types=1);

namespace SteveGrunwell\SemVer;

use SteveGrunwell\SemVer\Exceptions\InvalidVersionException;

/**
 * Represents a semantic version number.
 */
class Version
{
    /**
     * @var int The major version.
     */
    protected int $major;

    /**
     * @var int The minor version.
     */
    protected int $minor;

    /**
     * @var int The patch version.
     */
    protected int $patch;

    /**
     * @var string The pre-release version.
     */
    protected string $preRelease;

    /**
     * @var string Any build metadata.
     */
    protected string $buildMetadata;

    /**
     * @var string The original version string that was provided.
     */
    protected string $version;

    /**
     * Tracks whether or not $version has been parsed.
     */
    private bool $parsed = false;

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
     * Retrieve the string-ified version in major.minor.patch format.
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

        // Append the build metadata, if available.
        if ($this->buildMetadata) {
            $version .= '+' . $this->buildMetadata;
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
     * Get the build metadata.
     */
    public function getBuildMetadata(): string
    {
        return $this->parseVersion()->buildMetadata;
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
     * Set the build metadata.
     */
    public function setBuildMetadata(string $value): self
    {
        $this->parseVersion()->buildMetadata = $this->validateIdentifier($value);

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
     * @throws InvalidVersionException If $value is < 0.
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
     * Parse $this->version and populate the $major, $minor, $patch, $preRelease, and $buildMeta
     * properties on the instance.
     */
    protected function parseVersion(): self
    {
        // If we've already parsed once, just return.
        if ($this->parsed) {
            return $this;
        }

        // Before we even look at the format, check for any illegal characters.
        if (preg_match('/[^A-Za-z0-9-\+\.]/', $this->version)) {
            throw new InvalidVersionException(sprintf(
                '"%s" does not appear to be a valid version number.',
                $this->version
            ));
        }

        // Start by stripping off any build metadata.
        $parts = explode('+', $this->version, 2);
        $this->buildMetadata = $parts[1] ?? '';

        // Next, do the same for pre-release versions.
        $parts = explode('-', $parts[0] ?? '', 2);
        $this->preRelease = $parts[1] ?? '';

        // Everything left should be the major, minor, and/or patch versions.
        $values = explode('.', $parts[0] ?? '');
        $values = array_map('intval', $values);
        [$this->major, $this->minor, $this->patch] = array_pad($values, 3, 0);

        // Finally, mark the version as parsed so we don't need to do it again.
        $this->parsed = true;

        return $this;
    }

    /**
     * Validate permitted characters for pre-release versions and build metadata, both of which
     * have the same constraints.
     *
     * @link https://semver.org/spec/v2.0.0.html#spec-item-9
     * @link https://semver.org/spec/v2.0.0.html#spec-item-10
     *
     * @throws InvalidVersionException If any illegal characters are found.
     */
    protected function validateIdentifier(string $identifier): string
    {
        if (preg_match('/[^A-Za-z0-9-\.]/', $identifier)) {
            throw new InvalidVersionException('Identifiers may only contain ASCII alphanumerics, dots, and hyphens.');
        }

        // Look for any empty identifiers (multiple and/or trailing periods).
        if (preg_match('/\.[\.$]/', $identifier)) {
            throw new InvalidVersionException(sprintf(
                'Idenfiers may not be empty; "%s" appears to contain an extra "."',
                $identifier
            ));
        }

        return $identifier;
    }
}
