#!/usr/bin/env php
<?php

$gitName = run('git config user.name');
$authorName = ask('Author name', $gitName);

$gitEmail = run('git config user.email');
$authorEmail = ask('Author email', $gitEmail);

$usernameGuess = explode(':', run('git config remote.origin.url'))[1];
$usernameGuess = dirname($usernameGuess);
$usernameGuess = basename($usernameGuess);
$authorUsername = ask('Author username', $usernameGuess);

$vendorName = ask('Vendor name', $authorUsername);
$vendorSlug = slugify($vendorName);
$vendorNamespace = ucwords($vendorName);
$vendorNamespace = ask('Vendor namespace', $vendorNamespace);

$currentDirectory = getcwd();
$folderName = basename($currentDirectory);

$packageName = ask('Package name', $folderName);
$packageSlug = slugify($packageName);
$packageSlugWithoutPrefix = remove_prefix('laravel-', $packageSlug);

$className = title_case($packageName);
$className = ask('Class name', $className);
$description = ask('Package description', "This is my package {$packageSlug}");

$usePhpStan = confirm('Enable PhpStan?', true);
$usePint = confirm('Enable Pint?', true);
$useUpdateChangelogWorkflow = confirm('Use automatic changelog updater workflow?', true);

writeln('------');
writeln("Author     : {$authorName} ({$authorUsername}, {$authorEmail})");
writeln("Vendor     : {$vendorName} ({$vendorSlug})");
writeln("Package    : {$packageSlug} <{$description}>");
writeln("Namespace  : {$vendorNamespace}\\{$className}");
writeln("Class name : {$className}");
writeln('---');
writeln('Packages & Utilities');
writeln('Use PhpCsFixer       : ' . ($usePhpCsFixer ? 'yes' : 'no'));
writeln('Use Larastan/PhpStan : ' . ($usePhpStan ? 'yes' : 'no'));
writeln('Use Pint             : ' . ($usePint ? 'yes' : 'no'));
writeln('Use Auto-Changelog   : ' . ($useUpdateChangelogWorkflow ? 'yes' : 'no'));
writeln('------');

writeln('This script will replace the above values in all relevant files in the project directory.');

if (! confirm('Modify files?', true)) {
    exit(1);
}

$files = (str_starts_with(strtoupper(PHP_OS), 'WIN') ? replaceForWindows() : replaceForAllOtherOSes());

foreach ($files as $file) {
    replace_in_file($file, [
        ':author_name' => $authorName,
        ':author_username' => $authorUsername,
        'author@domain.com' => $authorEmail,
        ':vendor_name' => $vendorName,
        ':vendor_slug' => $vendorSlug,
        'VendorName' => $vendorNamespace,
        ':package_name' => $packageName,
        ':package_slug' => $packageSlug,
        ':package_slug_without_prefix' => $packageSlugWithoutPrefix,
        'Skeleton' => $className,
        'skeleton' => $packageSlug,
        ':package_description' => $description,
    ]);

    match (true) {
        str_contains($file, determineSeparator('src/Skeleton.php')) => rename($file, determineSeparator('./src/' . $className . '.php')),
        str_contains($file, determineSeparator('src/SkeletonServiceProvider.php')) => rename($file, determineSeparator('./src/' . $className . 'ServiceProvider.php')),
        str_contains($file, determineSeparator('src/Facades/Skeleton.php')) => rename($file, determineSeparator('./src/Facades/' . $className . '.php')),
        str_contains($file, determineSeparator('src/Commands/SkeletonCommand.php')) => rename($file, determineSeparator('./src/Commands/' . $className . 'Command.php')),
        str_contains($file, determineSeparator('database/migrations/create_skeleton_table.php.stub')) => rename($file, determineSeparator('./database/migrations/create_' . $packageSlugWithoutPrefix . '_table.php.stub')),
        str_contains($file, determineSeparator('config/skeleton.php')) => rename($file, determineSeparator('./config/' . $packageSlugWithoutPrefix . '.php')),
        str_contains($file, 'README.md') => remove_readme_paragraphs($file),
        default => [],
    };
}

if (! $usePint) {
    safeUnlink(__DIR__ . '/pint.json');
    safeUnlink(__DIR__ . '/.github/workflows/pint.yml');

    remove_composer_deps([
        'laravel/pint',
    ]);

    remove_composer_script(['pint']);
}

if (! $usePhpStan) {
    safeUnlink(__DIR__ . '/phpstan.neon.dist');
    safeUnlink(__DIR__ . '/phpstan-baseline.neon');
    safeUnlink(__DIR__ . '/.github/workflows/phpstan.yml');

    remove_composer_deps([
        'phpstan/extension-installer',
        'phpstan/phpstan-deprecation-rules',
        'phpstan/phpstan-phpunit',
        'nunomaduro/larastan',
    ]);

    remove_composer_script([
        'test:phpstan',
        '@test:phpstan',
    ]);
}

if (! $useUpdateChangelogWorkflow) {
    safeUnlink(__DIR__ . '/.github/workflows/update-changelog.yml');
}

confirm('Execute `composer install` and run tests?') && run('composer install && composer test');

confirm('Let this script delete itself?', true) && unlink(__FILE__);

function ask(string $question, string $default = ''): string
{
    $consoleColor = new ConsoleColor();
    $def = $default ? $consoleColor->apply('yellow', " ({$default})") : null;
    $answer = readline($consoleColor->apply('green', $question . $def . ': '));

    if (! $answer) {
        return $default;
    }

    return $answer;
}

function confirm(string $question, bool $default = false): bool
{
    $consoleColor = new ConsoleColor();

    $answer = ask($question, ($default ? 'Y/n' : 'y/N'));

    if (! $answer) {
        return $default;
    }

    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line . PHP_EOL;
}

function run(string $command): string
{
    return trim(shell_exec($command));
}

function str_after(string $subject, string $search): string
{
    $pos = strrpos($subject, $search);

    if ($pos === false) {
        return $subject;
    }

    return substr($subject, $pos + strlen($search));
}

function slugify(string $subject): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $subject), '-'));
}

function title_case(string $subject): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $subject)));
}

function replace_in_file(string $file, array $replacements): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        )
    );
}

function remove_prefix(string $prefix, string $content): string
{
    if (str_starts_with($content, $prefix)) {
        return substr($content, strlen($prefix));
    }

    return $content;
}

function remove_composer_deps(array $names)
{
    $data = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);

    foreach ($data['require-dev'] as $name => $version) {
        if (in_array($name, $names, true)) {
            unset($data['require-dev'][$name]);
        }
    }

    file_put_contents(__DIR__ . '/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function remove_composer_script(array $scriptNames)
{
    $data = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);

    foreach ($data['scripts'] as $name => $script) {
        if (is_array($script)) {
            foreach ($script as $k => $s) {
                if (in_array($s, $scriptNames)) {
                    unset($data['scripts'][$name][$k]);

                    break;
                }
            }
        } elseif (in_array($name, $scriptNames)) {
            unset($data['scripts'][$name]);

            break;
        }
    }

    file_put_contents(__DIR__ . '/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function remove_readme_paragraphs(string $file): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        preg_replace('/<!--delete-->.*<!--\/delete-->/s', '', $contents) ?: $contents
    );
}

function safeUnlink(string $filename)
{
    if (file_exists($filename) && is_file($filename)) {
        unlink($filename);
    }
}

function determineSeparator(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function replaceForWindows(): array
{
    return preg_split('/\\r\\n|\\r|\\n/', run('dir /S /B * | findstr /v /i .git\ | findstr /v /i vendor | findstr /v /i ' . basename(__FILE__) . ' | findstr /r /i /M /F:/ ":author :vendor :package VendorName skeleton vendor_name vendor_slug author@domain.com"'));
}

function replaceForAllOtherOSes(): array
{
    return explode(PHP_EOL, run('grep -E -r -l -i ":author|:vendor|:package|VendorName|skeleton|vendor_name|vendor_slug|author@domain.com" --exclude-dir=vendor ./* ./.github/* | grep -v ' . basename(__FILE__)));
}

class ConsoleColor
{
    const FOREGROUND = 38;

    const BACKGROUND = 48;

    const COLOR256_REGEXP = '~^(bg_)?color_([0-9]{1,3})$~';

    const RESET_STYLE = 0;

    /** @var bool */
    private $isSupported;

    /** @var bool */
    private $forceStyle = false;

    /** @var array */
    private $styles = [
        'none' => null,
        'bold' => '1',
        'dark' => '2',
        'italic' => '3',
        'underline' => '4',
        'blink' => '5',
        'reverse' => '7',
        'concealed' => '8',

        'default' => '39',
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'light_gray' => '37',

        'dark_gray' => '90',
        'light_red' => '91',
        'light_green' => '92',
        'light_yellow' => '93',
        'light_blue' => '94',
        'light_magenta' => '95',
        'light_cyan' => '96',
        'white' => '97',

        'bg_default' => '49',
        'bg_black' => '40',
        'bg_red' => '41',
        'bg_green' => '42',
        'bg_yellow' => '43',
        'bg_blue' => '44',
        'bg_magenta' => '45',
        'bg_cyan' => '46',
        'bg_light_gray' => '47',

        'bg_dark_gray' => '100',
        'bg_light_red' => '101',
        'bg_light_green' => '102',
        'bg_light_yellow' => '103',
        'bg_light_blue' => '104',
        'bg_light_magenta' => '105',
        'bg_light_cyan' => '106',
        'bg_white' => '107',
    ];

    /** @var array */
    private $themes = [];

    public function __construct()
    {
        $this->isSupported = $this->isSupported();
    }

    /**
     * @param  string|array  $style
     * @param  string  $text
     * @return string
     *
     * @throws InvalidStyleException
     * @throws \InvalidArgumentException
     */
    public function apply($style, $text)
    {
        if (! $this->isStyleForced() && ! $this->isSupported()) {
            return $text;
        }

        if (is_string($style)) {
            $style = [$style];
        }
        if (! is_array($style)) {
            throw new \InvalidArgumentException('Style must be string or array.');
        }

        $sequences = [];

        foreach ($style as $s) {
            if (isset($this->themes[$s])) {
                $sequences = array_merge($sequences, $this->themeSequence($s));
            } elseif ($this->isValidStyle($s)) {
                $sequences[] = $this->styleSequence($s);
            } else {
                throw new InvalidStyleException($s);
            }
        }

        $sequences = array_filter($sequences, function ($val) {
            return $val !== null;
        });

        if (empty($sequences)) {
            return $text;
        }

        return $this->escSequence(implode(';', $sequences)) . $text . $this->escSequence(self::RESET_STYLE);
    }

    /**
     * @param  bool  $forceStyle
     */
    public function setForceStyle($forceStyle)
    {
        $this->forceStyle = (bool) $forceStyle;
    }

    /**
     * @return bool
     */
    public function isStyleForced()
    {
        return $this->forceStyle;
    }

    /**
     * @param  array  $themes
     *
     * @throws InvalidStyleException
     * @throws \InvalidArgumentException
     */
    public function setThemes(array $themes)
    {
        $this->themes = [];
        foreach ($themes as $name => $styles) {
            $this->addTheme($name, $styles);
        }
    }

    /**
     * @param  string  $name
     * @param  array|string  $styles
     *
     * @throws \InvalidArgumentException
     * @throws InvalidStyleException
     */
    public function addTheme($name, $styles)
    {
        if (is_string($styles)) {
            $styles = [$styles];
        }
        if (! is_array($styles)) {
            throw new \InvalidArgumentException('Style must be string or array.');
        }

        foreach ($styles as $style) {
            if (! $this->isValidStyle($style)) {
                throw new InvalidStyleException($style);
            }
        }

        $this->themes[$name] = $styles;
    }

    /**
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function hasTheme($name)
    {
        return isset($this->themes[$name]);
    }

    /**
     * @param  string  $name
     */
    public function removeTheme($name)
    {
        unset($this->themes[$name]);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function isSupported()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            // phpcs:ignore Generic.PHP.NoSilencedErrors,PHPCompatibility.FunctionUse.NewFunctions.sapi_windows_vt100_supportFound
            if (function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT)) {
                return true;
            } elseif (getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON') {
                return true;
            }

            return false;
        } else {
            // phpcs:ignore Generic.PHP.NoSilencedErrors
            return function_exists('posix_isatty') && @posix_isatty(STDOUT);
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function are256ColorsSupported()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            // phpcs:ignore Generic.PHP.NoSilencedErrors,PHPCompatibility.FunctionUse.NewFunctions.sapi_windows_vt100_supportFound
            return function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT);
        } else {
            return strpos(getenv('TERM'), '256color') !== false;
        }
    }

    /**
     * @return array
     */
    public function getPossibleStyles()
    {
        return array_keys($this->styles);
    }

    /**
     * @param  string  $name
     * @return string[]
     */
    private function themeSequence($name)
    {
        $sequences = [];
        foreach ($this->themes[$name] as $style) {
            $sequences[] = $this->styleSequence($style);
        }

        return $sequences;
    }

    /**
     * @param  string  $style
     * @return string
     */
    private function styleSequence($style)
    {
        if (array_key_exists($style, $this->styles)) {
            return $this->styles[$style];
        }

        if (! $this->are256ColorsSupported()) {
            return null;
        }

        preg_match(self::COLOR256_REGEXP, $style, $matches);

        $type = $matches[1] === 'bg_' ? self::BACKGROUND : self::FOREGROUND;
        $value = $matches[2];

        return "$type;5;$value";
    }

    /**
     * @param  string  $style
     * @return bool
     */
    private function isValidStyle($style)
    {
        return array_key_exists($style, $this->styles) || preg_match(self::COLOR256_REGEXP, $style);
    }

    /**
     * @param  string|int  $value
     * @return string
     */
    private function escSequence($value)
    {
        return "\033[{$value}m";
    }
}
