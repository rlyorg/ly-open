<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Irazasyed\LaravelGAMP\Facades\GAMP;

class GampQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clientId;
    protected $category;
    protected $action;
    protected $label;

    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clientId, $category, $action, $label)
    {
        $this->clientId = $clientId;
        $this->category = $category;
        $this->action   = $action;
        $this->label    = $label;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // https://github.com/irazasyed/laravel-gamp
        $gamp = GAMP::setClientId( $this->clientId );
        $gamp->setEventCategory($this->category)
            ->setEventAction($this->action)
            ->setEventLabel($this->label)
            ->sendEvent();
    }
}
