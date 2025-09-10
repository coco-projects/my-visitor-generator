<?php

    /**
     * Matomo - free/libre analytics platform
     *
     * @link    https://matomo.org
     * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
     *
     */

    namespace Piwik\Plugins\MyVisitorGenerator;

    use Piwik\ArchiveProcessor\Rules;
    use Piwik\Common;
    use Piwik\Nonce;
    use Piwik\Notification;
    use Piwik\Piwik;
    use Piwik\Plugin\ControllerAdmin;
    use Piwik\Plugins\SitesManager\API as SitesManagerAPI;
    use Piwik\SettingsServer;
    use Piwik\Site;
    use Piwik\Timer;
    use Piwik\View;

    class Controller extends ControllerAdmin
    {

    }
