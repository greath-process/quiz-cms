<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('view_any_lesson');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Lesson $lesson)
    {
        return $user->can('view_lesson');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create_lesson');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Lesson $lesson)
    {
        return $user->can('update_lesson');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Lesson $lesson)
    {
        return $user->can('delete_lesson');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_lesson');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Lesson $lesson)
    {
        return $user->can('force_delete_lesson');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDeleteAny(User $user)
    {
        return $user->can('force_delete_any_lesson');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Lesson $lesson)
    {
        return $user->can('restore_lesson');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restoreAny(User $user)
    {
        return $user->can('restore_any_lesson');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replicate(User $user, Lesson $lesson)
    {
        return $user->can('replicate_lesson');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reorder(User $user)
    {
        return $user->can('reorder_lesson');
    }

}
