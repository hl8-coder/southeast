<?php
namespace App\Models\Traits;

trait CreatesWithLock
{
    /**
     * 加锁版updateOrCreate
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public static function updateOrCreate(array $attributes, array $values = [])
    {
        return static::advisoryLock(function () use ($attributes, $values) {
            // emulate the code found in Illuminate\Database\Eloquent\Builder
            return (new static)->newQuery()->updateOrCreate($attributes, $values);
        }, $attributes);
    }

    /**
     * 加锁版firstOrCreate
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public static function firstOrCreate(array $attributes, array $values = [])
    {
        return static::advisoryLock(function () use ($attributes, $values) {
            return (new static)->newQuery()->firstOrCreate($attributes, $values);
        }, $attributes);
    }

    /**
     * In that case the $lockName, and default lock duration are pased in as arguments.
     */
    private static function advisoryLock(callable $callback, $attributes)
    {
        // Lock name based on Model.
        $lockName = substr(static::class . implode('-', array_values($attributes)), -64);

        // Lock for at most 10 seconds.  This is the MySQL >5.7.5 implementation.
        // Older MySQL versions have some weird behavior with GET_LOCK().
        // Other databases have a different implementation.
        \DB::statement("SELECT GET_LOCK('" . $lockName . "', 5)");

        $output = $callback();

        \DB::statement("SELECT RELEASE_LOCK('" . $lockName . "')");
        return $output;
    }
}