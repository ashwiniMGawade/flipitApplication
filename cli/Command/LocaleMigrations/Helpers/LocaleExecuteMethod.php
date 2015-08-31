<?php

namespace Command\LocaleMigrations\Helpers;

use Core\Service\LocaleLister;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input as Input;

trait LocaleExecuteMethod
{
    use LocaleExecuteCommonLogic;

    public function execute(Input\InputInterface $input, OutputInterface $output)
    {
        $locales = (new LocaleLister)->getAllLocales();

        foreach ($locales as $locale) {
            $this->runCommand($input, $output, $locale);
        }
    }
}
