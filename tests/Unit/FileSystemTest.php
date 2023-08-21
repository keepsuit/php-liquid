<?php

use Keepsuit\Liquid\Exceptions\FileSystemException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\FileSystems\LocalFileSystem;

test('default', function () {
    expect(fn () => (new BlankFileSystem())->readTemplateFile('dummy'))
        ->toThrow(FileSystemException::class);
});

test('local', function () {
    $fileSystem = new LocalFileSystem('/some/path');

    expect($fileSystem)
        ->fullPath('mypartial')->toBe('/some/path/_mypartial.liquid')
        ->fullPath('dir/mypartial')->toBe('/some/path/dir/_mypartial.liquid');

    expect(fn () => $fileSystem->fullPath('../dir/mypartial'))->toThrow(FileSystemException::class);

    expect(fn () => $fileSystem->fullPath('/dir/../../dir/mypartial'))->toThrow(FileSystemException::class);

    expect(fn () => $fileSystem->fullPath('/etc/passwd'))->toThrow(FileSystemException::class);
});

test('custom template filename patterns', function () {
    $fileSystem = new LocalFileSystem('/some/path', '%s.html');

    expect($fileSystem)
        ->fullPath('mypartial')->toBe('/some/path/mypartial.html')
        ->fullPath('dir/mypartial')->toBe('/some/path/dir/mypartial.html');
});
