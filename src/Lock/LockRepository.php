<?php namespace Anomaly\Streams\Platform\Lock;

use Anomaly\Streams\Platform\Lock\Contract\LockInterface;
use Anomaly\Streams\Platform\Lock\Contract\LockRepositoryInterface;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Anomaly\Streams\Platform\Model\EloquentRepository;
use Carbon\Carbon;

/**
 * Class LockRepository
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class LockRepository extends EloquentRepository implements LockRepositoryInterface
{

    /**
     * The lock model.
     *
     * @var LockModel
     */
    protected $model;

    /**
     * Create a new LockRepository instance.
     *
     * @param LockModel $model
     */
    public function __construct(LockModel $model)
    {
        $this->model = $model;
    }

    /**
     * Find a lock by model.
     *
     * @param EloquentModel $model
     * @return null|LockInterface
     */
    public function findByLockable(EloquentModel $model)
    {
        return $this->model
            ->where('lockable_type', get_class($model))
            ->where('lockable_id', $model->getId())
            ->first();
    }

    /**
     * Touch locks by URL and session.
     *
     * @param $url
     * @param $session
     * @return $this
     */
    public function touchLocks($url, $session)
    {
        $this->model
            ->where('url', $url)
            ->where('session_id', $session)
            ->update(['locked_at' => new Carbon(null, config('streams::datetime.database_timezone'))]);
    }

    /**
     * Release locks by URL and session.
     *
     * @param $url
     * @param $session
     * @return $this
     */
    public function releaseLocks($url, $session)
    {
        $this->model
            ->where('url', $url)
            ->where('session_id', $session)
            ->delete();
    }

    /**
     * Clean up old lock files.
     */
    public function cleanup()
    {
        $this->model
            ->where('locked_at', '<=', new Carbon('-1 minute', config('streams::datetime.database_timezone')))
            ->delete();
    }

}
