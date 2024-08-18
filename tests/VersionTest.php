<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;
use SteveGrunwell\SemVer\Exceptions\InvalidVersionException;
use SteveGrunwell\SemVer\Version;

#[CoversClass(Version::class)]
class VersionTest extends TestCase
{
    #[Test]
    #[Group('Getters')]
    public function casting_to_a_string_should_call_getVersion(): void
    {
        $version = new Version();
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);

        $this->assertSame('1.2.3', (string) $version);
    }

    #[Test]
    #[Group('Getters')]
    public function getVersion_should_retrieve_the_collapsed_string(): void
    {
        $version = new Version();
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);

        $this->assertSame('1.2.3', $version->getVersion());
    }

    #[Test]
    #[Group('Getters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function getVersion_should_include_the_prerelease_when_available(): void
    {
        $version = new Version();
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);
        $version->setPreReleaseVersion('alpha');

        $this->assertSame('1.2.3-alpha', $version->getVersion());
    }

    #[Test]
    #[Group('Getters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/2')]
    public function getVersion_should_include_the_build_metadata_when_available(): void
    {
        $version = new Version();
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);
        $version->setBuildMetadata('abc123');

        $this->assertSame('1.2.3+abc123', $version->getVersion());
    }

    #[Test]
    #[Group('Getters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/2')]
    public function getVersion_should_include_the_prerelease_and_build_metadata_when_available(): void
    {
        $version = new Version();
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);
        $version->setPreReleaseVersion('alpha');
        $version->setBuildMetadata('abc123');

        $this->assertSame('1.2.3-alpha+abc123', $version->getVersion());
    }

    #[Test]
    public function getVersion_should_throw_when_unable_to_parse_the_version(): void
    {
        $this->expectException(InvalidVersionException::class);

        (new Version('this is not a valid version'))->getVersion();
    }

    #[Test]
    #[Group('Getters')]
    public function getMajorVersion_should_return_the_major_version(): void
    {
        $version = new Version('1.2.3');

        $this->assertSame(1, $version->getMajorVersion());
    }

    #[Test]
    #[Group('Getters')]
    public function getMinorVersion_should_return_the_minor_version(): void
    {
        $version = new Version('1.2.3');

        $this->assertSame(2, $version->getMinorVersion());
    }

    #[Test]
    #[Group('Getters')]
    public function getPatchVersion_should_return_the_patch_version(): void
    {
        $version = new Version('1.2.3');

        $this->assertSame(3, $version->getPatchVersion());
    }

    #[Test]
    #[DataProvider('provide_version_getters_and_setters')]
    #[Group('Getters')]
    public function digit_getters_should_default_to_zero(string $getter): void
    {
        $this->assertSame(0, (new Version())->{$getter}());
    }

    #[Test]
    #[Group('Getters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function getPreReleaseVersion_should_return_the_prerelease_version(): void
    {
        $version = new Version('1.2.3-alpha');

        $this->assertSame('alpha', $version->getPreReleaseVersion());
    }

    #[Test]
    #[Group('Getters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function getBuildMetadata_should_return_the_build_metadata(): void
    {
        $version = new Version('1.2.3+abc123');

        $this->assertSame('abc123', $version->getBuildMetadata());
    }

    #[Test]
    #[Group('Getters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function getPreReleaseVersion_should_default_to_an_empty_string(): void
    {
        $version = new Version('1.2.3');

        $this->assertSame('', $version->getPreReleaseVersion());
    }

    #[Test]
    #[Group('Setters')]
    public function setMajorVersion_changes_the_major_version(): void
    {
        $version = new Version('1.2.3');
        $version->setMajorVersion(2);

        $this->assertSame('2.2.3', $version->getVersion());
    }

    #[Test]
    #[Group('Setters')]
    public function setMinorVersion_changes_the_minor_version(): void
    {
        $version = new Version('1.2.3');
        $version->setMinorVersion(3);

        $this->assertSame('1.3.3', $version->getVersion());
    }

    #[Test]
    #[Group('Setters')]
    public function setPatchVersion_changes_the_patch_version(): void
    {
        $version = new Version('1.2.3');
        $version->setPatchVersion(4);

        $this->assertSame('1.2.4', $version->getVersion());
    }

    #[Test]
    #[Group('Setters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function setPreReleaseVersion_sets_the_prerelease_version(): void
    {
        $version = new Version('1.2.3-alpha');
        $version->setPreReleaseVersion('beta');

        $this->assertSame('1.2.3-beta', $version->getVersion());
    }

    #[Test]
    #[DataProvider('provide_version_getters_and_setters')]
    #[Group('Setters')]
    public function digit_setters_should_not_accept_non_negative_values(string $getter, string $setter): void
    {
        $this->expectException(InvalidVersionException::class);

        (new Version())->{$setter}(-2);
    }

    /**
     * @link https://semver.org/spec/v2.0.0.html#spec-item-9
     */
    #[Test]
    #[TestDox('Pre-release versions may only contain alphanumeric characters, hyphens, and dots')]
    #[DataProvider('provide_invalid_identifiers')]
    #[Group('Setters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function prerelease_versions_should_be_validated(string $identifier): void
    {
        $this->expectException(InvalidVersionException::class);

        (new Version())->setPreReleaseVersion($identifier);
    }

    /**
     * @link https://semver.org/spec/v2.0.0.html#spec-item-9
     */
    #[Test]
    #[TestDox('Pre-release versions may only contain alphanumeric characters, hyphens, and dots')]
    #[DataProvider('provide_invalid_build_metadata')]
    #[Group('Setters')]
    #[Ticket('https://github.com/stevegrunwell/semver-parser/issues/1')]
    public function build_metadata_should_be_validated(string $identifier): void
    {
        $this->expectException(InvalidVersionException::class);

        (new Version())->setBuildMetadata($identifier);
    }

    #[Test]
    #[DataProvider('provide_version_getters_and_setters')]
    #[Group('Setters')]
    public function digit_values_can_be_incremented(string $getter, string $setter): void
    {
        $version = new Version();
        $method  = 'increment' . substr($setter, 3);
        $version->{$setter}(1);
        $version->{$method}();

        $this->assertSame(2, $version->{$getter}());
    }

    /**
     * @link https://semver.org/spec/v2.0.0.html#spec-item-8
     */
    #[Test]
    #[Group('Setters')]
    public function incrementMajorVersion_should_reset_minor_and_patch_versions_to_zero(): void
    {
        $version = new Version('1.2.3');

        $version->incrementMajorVersion();

        $this->assertSame(0, $version->getMinorVersion());
        $this->assertSame(0, $version->getPatchVersion());
    }

    /**
     * @link https://semver.org/spec/v2.0.0.html#spec-item-7
     */
    #[Test]
    #[Group('Setters')]
    public function incrementMinorVersion_should_reset_patch_versions_to_zero(): void
    {
        $version = new Version('1.2.3');

        $version->incrementMajorVersion();

        $this->assertSame(0, $version->getPatchVersion());
    }

    #[Test]
    #[DataProvider('provide_version_getters_and_setters')]
    #[Group('Setters')]
    public function digit_values_can_be_decremented(string $getter, string $setter): void
    {
        $version = new Version();
        $method  = 'decrement' . substr($setter, 3);
        $version->{$setter}(2);
        $version->{$method}();

        $this->assertSame(1, $version->{$getter}());
    }

    #[Test]
    #[DataProvider('provide_version_getters_and_setters')]
    #[Group('Setters')]
    public function digit_values_cannot_be_decremented_below_zero(string $getter, string $setter): void
    {
        $version = new Version();
        $method  = 'decrement' . substr($setter, 3);
        $version->{$setter}(0);

        $this->expectException(InvalidVersionException::class);

        $version->{$method}();
    }

    #[Test]
    #[DataProvider('provide_control_versions')]
    public function all_examples_from_the_specification_should_pass(
        string $version,
        int $major,
        int $minor,
        int $patch,
        string $prerelease
    ): void {
        $version = new Version($version);

        $this->assertSame($major, $version->getMajorVersion());
        $this->assertSame($minor, $version->getMinorVersion());
        $this->assertSame($patch, $version->getPatchVersion());
        $this->assertSame($prerelease, $version->getPreReleaseVersion());
    }

    /**
     * As a control, include examples given in the specification.
     *
     * @return iterable<array{string, int, int, int, string, string}>
     */
    public static function provide_control_versions(): iterable
    {
        return [
            // https://semver.org/spec/v2.0.0.html#spec-item-9
            ['1.0.0-alpha', 1, 0, 0, 'alpha', ''],
            ['1.0.0-alpha.1', 1, 0, 0, 'alpha.1', ''],
            ['1.0.0-0.3.7', 1, 0, 0, '0.3.7', ''],
            ['1.0.0-x.7.z.92', 1, 0, 0, 'x.7.z.92', ''],
            ['1.0.0-alpha+001', 1, 0, 0, 'alpha', '001'],
            ['1.0.0+20130313144700', 1, 0, 0, '', '20130313144700'],
            ['1.0.0-beta+exp.sha.5114f85', 1, 0, 0, 'beta', 'exp.sha.5114f85'],

            // If digits are missing, treat them as zeros.
            ['1.0', 1, 0, 0, '', ''],
            ['1', 1, 0, 0, '', ''],
            ['1-alpha', 1, 0, 0, 'alpha', ''],
            ['1-alpha.1', 1, 0, 0, 'alpha.1', ''],
            ['1-alpha.1+abc.123', 1, 0, 0, 'alpha.1', 'abc.123'],
        ];
    }

    /**
     * Return all of the available version setter methods.
     *
     * @return iterable<string, array{string, string}>>
     */
    public static function provide_version_getters_and_setters(): iterable
    {
        return [
            'Major' => ['getMajorVersion', 'setMajorVersion'],
            'Minor' => ['getMinorVersion', 'setMinorVersion'],
            'Patch' => ['getPatchVersion', 'setPatchVersion'],
        ];
    }

    /**
     * Provide invalid pre-release version identifiers.
     *
     * @return iterable<string, array{string}>
     */
    public static function provide_invalid_identifiers(): iterable
    {
        yield 'Whitespace'  => ['alpha v1'];
        yield 'Underscores' => ['alpha_v1'];
        yield 'Empty dots'  => ['alpha..123'];
    }

    /**
     * Provide invalid pre-release version identifiers.
     *
     * @return iterable<string, array{string}>
     */
    public static function provide_invalid_build_metadata(): iterable
    {
        yield 'Whitespace'  => ['January 01'];
        yield 'Underscores' => ['abc_123'];
        yield 'Empty dots'  => ['abc..123'];
    }
}
