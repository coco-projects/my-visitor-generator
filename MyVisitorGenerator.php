<?php

    namespace Piwik\Plugins\MyVisitorGenerator;

    use Piwik\Plugin;

    include_once __DIR__ . '/vendor/autoload.php';

    class MyVisitorGenerator extends Plugin
    {
        public function registerEvents()
        {
            return [
                'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            ];
        }

        public function getClientSideTranslationKeys(&$result)
        {
            $result[] = 'VisitorGenerator_VisitorGenerator';
            $result[] = 'VisitorGenerator_PluginDescription';
            $result[] = 'VisitorGenerator_CliToolUsage';
            $result[] = 'VisitorGenerator_OverwriteLogFiles';
            $result[] = 'VisitorGenerator_DaysToCompute';
            $result[] = 'VisitorGenerator_GenerateFakeActions';
            $result[] = 'VisitorGenerator_AreYouSure';
            $result[] = 'VisitorGenerator_Warning';
            $result[] = 'VisitorGenerator_NotReversible';
            $result[] = 'VisitorGenerator_ChoiceYes';
            $result[] = 'VisitorGenerator_PleaseBePatient';
            $result[] = 'VisitorGenerator_LogImporterNote';
            $result[] = 'VisitorGenerator_Submit';
            $result[] = 'VisitorGenerator_GeneratedVisitsFor';
            $result[] = 'VisitorGenerator_NumberOfGeneratedActions';
            $result[] = 'VisitorGenerator_NbRequestsPerSec';
            $result[] = 'VisitorGenerator_AutomaticReprocess';
            $result[] = 'VisitorGenerator_ReRunArchiveScript';
        }
    }
