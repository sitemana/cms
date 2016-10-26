<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\app\helpers;

use Craft;
use craft\app\errors\MissingComponentException;
use craft\app\mail\Mailer;
use craft\app\mail\transportadapters\BaseTransportAdapter;
use craft\app\mail\transportadapters\Php;
use craft\app\mail\transportadapters\TransportAdapterInterface;
use craft\app\models\MailSettings;

/**
 * Class DateTimeHelper
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class MailerHelper
{
    /**
     * Creates a transport adapter based on the given mail settings.
     *
     * @param string     $type
     * @param array|null $settings
     *
     * @return TransportAdapterInterface
     * @throws MissingComponentException if $type is missing
     */
    public static function createTransportAdapter($type, $settings = null)
    {
        /** @var BaseTransportAdapter $adapter */
        $adapter = Component::createComponent([
            'type' => $type,
            'settings' => $settings,
        ], TransportAdapterInterface::class);

        return $adapter;
    }

    /**
     * Creates a mailer component based on the given mail settings.
     *
     * @param MailSettings $settings
     *
     * @return Mailer
     */
    public static function createMailer(MailSettings $settings)
    {
        try {
            $adapter = self::createTransportAdapter($settings->transportType, $settings->transportSettings);
        } catch (MissingComponentException $e) {
            // Fallback to the PHP mailer
            $adapter = new Php();
        }

        $mailer = new Mailer([
            'from' => [$settings->fromEmail => $settings->fromName],
            'template' => $settings->template,
            'transport' => $adapter->getTransportConfig(),
        ]);

        return $mailer;
    }
}
