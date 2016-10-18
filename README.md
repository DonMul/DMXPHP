# DMX PHP
[![License](https://img.shields.io/badge/licence-GPLv3-brightgreen.svg)](https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3))
DMX PHP is writter because i wanted to experiment with DMX and see the capabilities within the language of PHP.

This implementation has not yet been tested since i haven't come round to it. If anyone can test it, please let me know your findings.

## Example Usage
After you installed this component with composer, you can, for example, update a channel to a specific value:

<pre>
$client = new \DMXPHP\Client\USB('COM3');
$universe = DMXPHP\Universe::createFromArray([
    [
        'startChannel' => 0,
        'channels' => 8,
        'name' => ''
    ]
], $client);

$universe->getEntityOnChannel(1)->updateChannel(1, 100);
</pre>

## Notes
For the USB interaction with PHP, i made use of Xowap's PHP-Serial implementation (https://github.com/Xowap/PHP-Serial)