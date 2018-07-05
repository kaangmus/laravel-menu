<?php

namespace Nurmanhabib\LaravelMenu;

use Illuminate\Support\Collection;
use Nurmanhabib\LaravelMenu\Concerns\NavCollectionManage;
use Nurmanhabib\Navigator\Activators\RequestActivator;
use Nurmanhabib\Navigator\NavCollection;

class Menu
{
    use NavCollectionManage;

    /**
     * @var Collection
     */
    protected $navigators;

    /**
     * @var Collection
     */
    protected $renders;

    /**
     * @var Collection
     */
    protected $views;

    /**
     * Menu constructor.
     * @param array $navigators
     */
    public function __construct($navigators = [])
    {
        $this->navigators = new Collection($navigators);
        $this->renders = new Collection;
        $this->views = new Collection;
    }

    /**
     * @param string $name
     * @param callable $callback
     * @return LaravelNavigator
     */
    public function make($name = 'default', callable $callback)
    {
        $navigator = new LaravelNavigator($menu = new NavCollection);
        $navigator->setActivator(new RequestActivator(request()));

        $this->navigators->put($name, $navigator);

        $this->setCurrent($menu);

        $callback($menu);

        $this->clearCurrent();

        return $navigator;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->navigators;
    }

    public function render($name = 'default')
    {
        return $this->get($name)->render();
    }

    /**
     * @param string $name
     * @param callable|null $callback
     * @return LaravelNavigator
     */
    public function get($name = 'default', callable $callback = null)
    {
        if (!$this->navigators->has($name)) {
            return new LaravelNavigator(new NavCollection);
        }

        $navigator = $this->navigators->get($name);

        if ($callback) {
            $callback($navigator->getOriginalMenu());
        }

        return $navigator;
    }

    /**
     * @param string $alias
     * @param string $view
     * @return Menu
     */
    public function registerView($alias, $view)
    {
        $this->views->put($alias, $view);

        return $this;
    }

    public function hasView($view)
    {
        return $this->views->has($view);
    }

    public function getView($view)
    {
        return $this->views->get($view, $view);
    }
}
