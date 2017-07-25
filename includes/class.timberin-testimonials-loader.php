<?php

class TimberinTestimonialsLoader {

    protected $actions;
    protected $filters;

    public function __construct() {
        $this->actions = [];
        $this->filters = [];
    }

    public function add_action( $hook, $component, $callback ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
    }

    public function add_filter( $hook, $component, $callback ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback );
    }

    private function add( $hooks, $hook, $component, $callback ) {
        $hooks[] = [
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        ];

        return $hooks;
    }

    public function run() {
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], [ $hook['component'], $hook['callback'] ], 99, 1 );
        }

        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], [ $hook['component'], $hook['callback'] ], 10, 2);
        }

    }
}