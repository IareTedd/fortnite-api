<?php

namespace Fortnite;

use Fortnite\FortniteClient;

use Fortnite\Model\Items;

class Profile
{
    private $access_token;

    public $account_id;

    public $stats;
    public $items;
    public $challenges;

    /**
     * Constructs a new Fortnite\Profile instance.
     *
     * @param string $access_token OAuth2 Access token
     * @param string $account_id Epic account id
     *
     * @throws Exception\InvalidGameModeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct($access_token, $account_id)
    {
        $this->access_token = $access_token;
        $this->account_id = $account_id;
        $data = $this->fetch();
        $this->items = new Items($data->items);
        $this->stats = new Stats($access_token, $account_id);
        $this->challenges = new Challenges($this->access_token, $data->items);

    }

    /**
     * Fetches profile data.
     *
     * @return object Profile data
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetch()
    {
        $data = FortniteClient::sendFortnitePostRequest(FortniteClient::FORTNITE_API . 'game/v2/profile/' . $this->account_id . '/client/QueryProfile?profileId=athena&rvn=-1',
            $this->access_token,
            new \StdClass());
        return $data->profileChanges[0]->profile;
    }

    /**
     * Get current user's friends on Unreal Engine.
     *
     * @param bool $includePending Include pending friend requests
     *
     * @return object Array of friends
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFriends($includePending = true)
    {
        $endpoint = FortniteClient::EPIC_FRIENDS_ENDPOINT . $this->account_id . ($includePending ? '?includePending=true' : '');
        $data = FortniteClient::sendUnrealClientGetRequest($endpoint, $this->access_token, true);

        return $data;
    }

    /**
     * Sends or accepts friend request on Unreal Engine.
     *
     * @param $accountId Friend's Account Id
     *
     * @return null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addFriend($accountId)
    {
        $endpoint = FortniteClient::EPIC_FRIENDS_ENDPOINT . $this->account_id . '/' . $accountId;
        $data = FortniteClient::sendUnrealClientPostRequest($endpoint, null, $this->access_token, true);

        return $data;
    }

    /**
     * Removes friend from a friend list on Unreal Engine.
     *
     * @param $accountId Friend's Account Id
     *
     * @return int 204 on success
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeFriend($accountId)
    {
        $endpoint = FortniteClient::EPIC_FRIENDS_ENDPOINT . $this->account_id . '/' . $accountId;
        $data = FortniteClient::sendFortniteDeleteRequest($endpoint, $this->access_token, true);

        return $data;
    }
}