<?php

namespace Dynamic\FoxyStripe\Migration;

use Dynamic\FoxyStripe\Model\OptionItem;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Versioned\Versioned;

/**
 * Publishes all existing OptionItem records to the Live stage.
 *
 * This task is required after adding the Versioned extension to OptionItem.
 * Without publishing, existing options will only exist in the Draft stage
 * and will not appear on the live site.
 *
 * Usage: sake dev/tasks/publish-option-items
 */
class PublishOptionItemsTask extends BuildTask
{
    /**
     * @var string
     */
    private static $segment = 'publish-option-items';

    /**
     * @var string
     */
    protected $title = 'Publish Option Items';

    /**
     * @var string
     */
    protected $description = 'Publishes all OptionItem records to the Live stage after adding the Versioned extension.';

    /**
     * @param mixed $request
     */
    public function run($request)
    {
        $options = Versioned::get_by_stage(OptionItem::class, Versioned::DRAFT);
        $count = 0;
        $errors = 0;

        $this->log("Found {$options->count()} OptionItem records in Draft stage.");

        foreach ($options as $option) {
            try {
                $option->publishSingle();
                $count++;
            } catch (\Exception $e) {
                $errors++;
                $this->log("ERROR publishing OptionItem #{$option->ID}: {$e->getMessage()}");
            }
        }

        $this->log("Published {$count} OptionItem records.");

        if ($errors > 0) {
            $this->log("WARNING: {$errors} records failed to publish.");
        }

        $this->log('Done.');
    }

    /**
     * @param string $message
     */
    protected function log(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
