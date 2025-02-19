<?php

namespace App;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Underscore\Underscore;

/**
 * @property EloquentCollection<Conversation> $conversations
 * @property User $user
 * @property Mailbox $mailbox
 * @property int $type
 *
 */
class Folder extends Model
{
    /**
     * Folders types (ids from HelpScout interface).
     */
    // const TYPE_OPEN = 10;
    const TYPE_UNASSIGNED = 1;
    // User specific
    const TYPE_MINE = 20;
    // User specific
    const TYPE_STARRED = 25;
    const TYPE_DRAFTS = 30;
    const TYPE_ASSIGNED = 40;
    const TYPE_CLOSED = 60;
    const TYPE_DELETED = 70;
    const TYPE_SPAM = 80;

    public static $types = [
        // self::TYPE_OPEN => 'Open',
        self::TYPE_UNASSIGNED => 'Unassigned',
        self::TYPE_MINE       => 'Mine',
        self::TYPE_STARRED    => 'Starred',
        self::TYPE_DRAFTS     => 'Drafts',
        self::TYPE_ASSIGNED   => 'Assigned',
        self::TYPE_CLOSED     => 'Closed',
        self::TYPE_SPAM       => 'Spam',
        self::TYPE_DELETED    => 'Deleted',
    ];

    /**
     * https://glyphicons.bootstrapcheatsheets.com/.
     */
    public static $type_icons = [
        // self::TYPE_OPEN => 'open',
        self::TYPE_UNASSIGNED => 'folder-open',
        self::TYPE_MINE       => 'hand-right',
        self::TYPE_DRAFTS     => 'duplicate',
        self::TYPE_ASSIGNED   => 'user',
        self::TYPE_CLOSED     => 'lock', // lock
        self::TYPE_SPAM       => 'ban-circle',
        self::TYPE_DELETED    => 'trash',
        self::TYPE_STARRED    => 'star',
    ];

    // Public non-user specific mailbox types
    public static $public_types = [
        // self::TYPE_OPEN,
        self::TYPE_UNASSIGNED,
        self::TYPE_DRAFTS,
        self::TYPE_ASSIGNED,
        self::TYPE_CLOSED,
        self::TYPE_SPAM,
        self::TYPE_DELETED,
    ];

    // Folder types which belong to specific user.
    // These folders has user_id specified.
    public static $personal_types = [
        // self::TYPE_OPEN,
        self::TYPE_MINE,
        self::TYPE_STARRED,
    ];

    // Folder types to which conversations are added via conversation_folder table.
    public static $indirect_types = [
        // self::TYPE_OPEN,
        self::TYPE_DRAFTS,
        self::TYPE_STARRED,
    ];

    // Counter mode.
    const COUNTER_ACTIVE = 1;
    const COUNTER_TOTAL  = 2;

    public $timestamps = false;

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the mailbox to which folder belongs.
     */
    public function mailbox(): BelongsTo
    {
        return $this->belongsTo('App\Mailbox');
    }

    /**
     * Get the user to which folder belongs.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get starred conversations.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany('App\Conversation');
    }

    /**
     * @return array|string|Underscore|null
     */
    public function getTypeName()
    {
        // To make name translatable.
        switch ($this->type) {
            // case self::TYPE_OPEN:
            //     return __('Open');
            case self::TYPE_UNASSIGNED:
                return __('Unassigned');
            case self::TYPE_MINE:
                return __('Mine');
            case self::TYPE_DRAFTS:
                return __('Drafts');
            case self::TYPE_ASSIGNED:
                return __('Assigned');
            case self::TYPE_CLOSED:
                return __('Closed');
            case self::TYPE_SPAM:
                return __('Spam');
            case self::TYPE_DELETED:
                return __('Deleted');
            case self::TYPE_STARRED:
                return __('Starred');
            default:
                return __(\Eventy::filter('folder.type_name', self::$types[$this->type] ?? '', $this));
        }
    }

    public function getTypeIcon()
    {
        return \Eventy::filter('folder.type_icon', self::$type_icons[$this->type] ?? '', $this);
    }

    /**
     * Get order by array.
     *
     * @return array
     */
    public function getOrderByArray(): array
    {
        $order_by = [];

        switch ($this->type) {
            // case self::TYPE_OPEN:
            case self::TYPE_UNASSIGNED:
            case self::TYPE_MINE:
            case self::TYPE_STARRED:
            case self::TYPE_ASSIGNED:
                $order_by[] = ['status' => 'asc'];
                $order_by[] = ['last_reply_at' => 'desc'];
                break;

            case self::TYPE_DRAFTS:
                $order_by = [['updated_at' => 'desc']];
                break;

            case self::TYPE_CLOSED:
                $order_by = [['closed_at' => 'desc']];
                break;

            case self::TYPE_SPAM:
                $order_by = [['last_reply_at' => 'desc']];
                break;

            case self::TYPE_DELETED:
                $order_by = [['user_updated_at' => 'desc']];
                break;

            default:
                $order_by = \Eventy::filter('folder.conversations_order_by', $order_by, $this->type);
                break;
        }

        // Process columns sorting.
        $sorting = Conversation::getConvTableSorting();
        if ($sorting['sort_by'] == 'date') {
            if ($sorting['order'] != 'desc') {
                foreach ($order_by as $block_i => $block) {
                    foreach ($block as $field => $order) {
                        if ($field == 'status') {
                            unset($order_by[$block_i][$field]);
                        } else {
                            if ($order == 'desc') {
                                $order_by[$block_i][$field] = 'asc';
                            }
                        }
                    }
                }
            }
        } else {
            $order_by = [[$sorting['sort_by'] => $sorting['order']]];
        }

        return $order_by;
    }

    /**
     * Add order by to the query.
     */
    public function queryAddOrderBy($query)
    {
        $order_bys = $this->getOrderByArray();
        foreach ($order_bys as $order_by) {
            foreach ($order_by as $field => $sort_order) {
                $query->orderBy($field, $sort_order);
            }
        }

        return $query;
    }

    /**
     * Is this folder accumulates conversations via conversation_folder table.
     */
    public function isIndirect()
    {
        return in_array($this->type, self::$indirect_types);
    }

    public function updateCounters()
    {
        if (\Eventy::filter('folder.update_counters', false, $this)) {
            return;
        }
        if ($this->type == self::TYPE_MINE && $this->user_id) {
            $this->active_count = Conversation::where('user_id', $this->user_id)
                ->where('mailbox_id', $this->mailbox_id)
                ->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', Conversation::STATUS_ACTIVE)
                ->count();
            $this->total_count = Conversation::where('user_id', $this->user_id)
                ->where('mailbox_id', $this->mailbox_id)
                ->where('state', Conversation::STATE_PUBLISHED)
                ->count();
        } elseif ($this->type == self::TYPE_STARRED) {
            $this->active_count = count(Conversation::getUserStarredConversationIds($this->mailbox_id, $this->user_id));
            $this->total_count = $this->active_count;
        } elseif ($this->type == self::TYPE_DELETED) {
            $this->active_count = $this->conversations()->where('state', Conversation::STATE_DELETED)
                ->count();
            $this->total_count = $this->active_count;
        } elseif ($this->isIndirect()) {
            // Conversation are connected to folder via conversation_folder table.
            // Drafts.
            $this->active_count = ConversationFolder::where('conversation_folder.folder_id', $this->id)
                ->join('conversations', 'conversations.id', '=', 'conversation_folder.conversation_id')
                //->where('state', Conversation::STATE_PUBLISHED)
                ->count();
            $this->total_count = $this->active_count;
        } else {
            $this->active_count = $this->conversations()
                ->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', Conversation::STATUS_ACTIVE)
                ->count();
            $this->total_count = $this->conversations()
                ->where('state', Conversation::STATE_PUBLISHED)
                ->count();
        }
        $this->save();
    }

    /**
     * Get count to display in folders list.
     *
     * @param array $folders [description]
     *
     * @return [type] [description]
     */
    public function getCount($folders = [])
    {
        $counter = \Eventy::filter('folder.counter', self::COUNTER_ACTIVE, $this, $folders);

        $count = \Eventy::filter('folder.count', false, $this, $counter, $folders);
        if ($count !== false) {
            return $count;
        }

        if ($counter == self::COUNTER_TOTAL || $this->type == self::TYPE_STARRED || $this->type == self::TYPE_DRAFTS) {
            return $this->total_count;
        } else {
            return $this->getActiveCount($folders);
        }
    }

    /**
     * Get calculated number of active conversations.
     */
    public function getActiveCount($folders = [])
    {
        $active_count = $this->active_count;
        if ($this->type == self::TYPE_ASSIGNED) {
            $mine_folder = \Eventy::filter('folder.active_count_mine_folder', null, $this, $folders);

            if (!$mine_folder) {
                if ($folders) {
                    $mine_folder = $folders->firstWhere('type', self::TYPE_MINE);
                } elseif ($this->mailbox_id) {
                    $mine_folder = $this->mailbox->folders()->where('type', self::TYPE_MINE)->first();
                }
            }

            if ($mine_folder) {
                $active_count = $active_count - $mine_folder->active_count;
                if ($active_count < 0) {
                    $active_count = 0;
                }
            }
        }

        return $active_count;
    }

    /**
     * Query for waiting since.
     */
    public function getWaitingSinceQuery()
    {
        $query = null;

        if ($this->type == self::TYPE_MINE) {
            // Assigned to user.
            $query = Conversation::where('user_id', $this->user_id)
                ->where('mailbox_id', $this->mailbox_id);
        } elseif ($this->isIndirect()) {
            // Via intermediate table.
            $query = Conversation::join('conversation_folder', 'conversations.id', '=', 'conversation_folder.conversation_id')
                ->where('conversation_folder.folder_id', $this->id);
        } else {
            // All other conversations.
            $query = $this->conversations();
        }

        return \Eventy::filter('folder.waiting_since_query', $query, $this);
    }

    /**
     * Works for main folder only for now.
     *
     * @return [type] [description]
     */
    public function getWaitingSince()
    {
        // Get oldest active conversation.
        $conversation = $this->getWaitingSinceQuery()
            ->where('state', Conversation::STATE_PUBLISHED)
            ->where('status', Conversation::STATUS_ACTIVE)
            ->orderBy($this->getWaitingSinceField(), 'asc')
            ->first();
        if ($conversation) {
            return $conversation->getWaitingSince($this);
        } else {
            return '';
        }
    }

    /**
     * Get conversation field used to detect waiting since time.
     *
     * @return [type] [description]
     */
    public function getWaitingSinceField()
    {
        if ($this->type == \App\Folder::TYPE_CLOSED) {
            return 'closed_at';
        } elseif ($this->type == \App\Folder::TYPE_DRAFTS) {
            return 'updated_at';
        } elseif ($this->type == \App\Folder::TYPE_DELETED) {
            return 'user_updated_at';
        } else {
            return 'last_reply_at';
        }
    }

    public function url($mailbox_id)
    {
        return \Eventy::filter('folder.url', route('mailboxes.view.folder', ['id' => $mailbox_id, 'folder_id' => $this->id]), $mailbox_id, $this);
    }

    public static function create($data, $unique_per_user = true, $save = true)
    {
        if (!isset($data['mailbox_id']) || !isset($data['type'])) {
            return null;
        }
        $folder = new Folder();
        $folder->mailbox_id = $data['mailbox_id'];
        $folder->type = $data['type'];

        if (!empty($data['user_id'])) {
            if ($unique_per_user) {
                $user_folder = Folder::where('mailbox_id', $data['mailbox_id'])
                    ->where('user_id', $data['user_id'])
                    ->where('type', $data['type'])
                    ->first();
                // User folder already exists.
                if ($user_folder) {
                    return $user_folder;
                }
            }
            $folder->user_id = $data['user_id'];
        }

        if ($save) {
            $folder->save();
        }

        return $folder;
    }

    /**
     * Get meta value.
     */
    public function getMeta($key, $default = null)
    {
        $metas = $this->meta;
        if (isset($metas[$key])) {
            return $metas[$key];
        } else {
            return $default;
        }
    }

    /**
     * Set meta value.
     */
    public function setMeta($key, $value)
    {
        $metas = $this->meta;
        $metas[$key] = $value;
        $this->meta = $metas;
    }

    /**
     * Unset thread meta value.
     */
    public function unsetMeta($key)
    {
        $metas = $this->meta;
        if (isset($metas[$key])) {
            unset($metas[$key]);
            $this->meta = $metas;
        }
    }
}
