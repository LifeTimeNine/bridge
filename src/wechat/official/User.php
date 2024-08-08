<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\official;

use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\Request;

/**
 * 公众号用户管理
 * @throws InvalidConfigException
 */
class User extends Basic
{
    /**
     * 创建标签
     * @access  public
     * @param   string  $name   标签名称
     * @return  array
     * @throws InvalidResponseException
     */
    public function createTag(string $name): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/create', [], [
            'tag' => ['name' => $name]
        ]);
    }

    /**
     * 获取已经创建的标签
     * @access  public
     * @return  array
     * @throws InvalidResponseException
     */
    public function getTag(): array
    {
        return $this->request(Request::METHOD_GET, 'https://api.weixin.qq.com/cgi-bin/tags/get');
    }

    /**
     * 更新标签信息
     * @access  public
     * @param   int     $tagId  标签ID
     * @param   string  $name   名称
     * @return  array
     * @throws InvalidResponseException
     */
    public function updateTag(int $tagId, string $name): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/update', [], [
            'tag' => ['id' => $tagId, 'name' => $name]
        ]);
    }

    /**
     * 删除标签
     * @access  public
     * @param   int     $tagId     标签ID
     * @return  array
     * @throws InvalidResponseException
     */
    public function deleteTag(int $tagId): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/delete', [], [
            'tag' => ['id' => $tagId]
        ]);
    }

    /**
     * 获取某个标签下的用户列表
     * @access  public
     * @param   int     $tagId          微信标签id
     * @param   string  $nextOpenid    第一个拉取的OPENID，不填默认从头开始拉取
     * @return array
     * @throws InvalidResponseException
     */
    public function getTagUser(int $tagId, string $nextOpenid = null):array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/user/tag/get', [], [
            'tagid'=> $tagId,
            'next_openid' => $nextOpenid
        ]);
    }

    /**
     * 批量为用户绑定标签
     * @access  public
     * @param   int     $tagId          微信标签id
     * @param   array   $openidList     用户OpenID列表
     * @return  array
     * @throws InvalidResponseException
     */
    public function batchBindTag(int $tagId, array $openidList): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging', [], [
            'tagid' => $tagId,
            'openid_list' => $openidList
        ]);
    }

    /**
     * 批量为用户解绑标签
     * @access  public
     * @param   int     $tagId          微信标签id
     * @param   array   $openidList     用户OpenID列表
     * @return  array
     * @throws InvalidResponseException
     */
    public function batchUnBindTag(int $tagId, array $openidList): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging', [], [
            'tagid' => $tagId,
            'openid_list' => $openidList
        ]);
    }

    /**
     * 获取用户绑定的标签
     * @access  public
     * @param   string  $openid     用户OpenID
     * @return  array
     * @throws InvalidResponseException
     */
    public function getUserTag(string $openid): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/getidlist', [], [
            'openid' => $openid
        ]);
    }

    /**
     * 设置用户备注名
     * @access  public
     * @param   string  $openid         用户OpenID
     * @param   string  $remark         备注名
     * @return  array
     * @throws InvalidResponseException
     */
    public function updateRemark(string $openid, string $remark): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark', [], [
            'openid' => $openid,
            'remark' => $remark
        ]);
    }

    /**
     * 获取用户基本信息(UnionID机制)
     * @access  public
     * @param   string  $openid     用户OpenID
     * @return  array
     * @throws InvalidResponseException
     */
    public function getUserInfo(string $openid): array
    {
        return $this->request(Request::METHOD_GET, 'https://api.weixin.qq.com/cgi-bin/user/info', [
            'openid' => $openid,
            'lang' => 'zh_CN'
        ]);
    }

    /**
     * 批量获取用户基本信息
     * @access  public
     * @param   array   $openidList     用户OpenID列表
     * @return  array
     * @throws InvalidResponseException
     */
    public function batchGetUserInfo(array $openidList): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/user/info/batchget', [], [
            'user_list' => array_map(function($v) {
                return ['openid' => $v];
            }, $openidList)
        ]);
    }

    /**
     * 获取用户列表
     * @access  public
     * @param   string  $nextOpenid    第一个拉取的OPENID，不填默认从头开始拉取
     * @return  array
     * @throws InvalidResponseException
     */
    public function getUserList(string $nextOpenid = null): array
    {
        return $this->request(Request::METHOD_GET, 'https://api.weixin.qq.com/cgi-bin/user/get', [
            'next_openid' => $nextOpenid
        ]);
    }

    /**
     * 获取黑名单列表
     * @access  public
     * @param   string  $beginOpenid    第一个拉取的OPENID，不填默认从头开始拉取
     * @return  array
     * @throws InvalidResponseException
     */
    public function getBlackList(string $beginOpenid = null): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist', [], [
            'begin_openid' => $beginOpenid
        ]);
    }

    /**
     * 批量拉黑用户
     * @access  public
     * @param   array   $openidList     用户OpenID列表
     * @return  array
     * @throws InvalidResponseException
     */
    public function batchBlack(array $openidList): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist', [], [
            'openid_list' => $openidList
        ]);
    }

    /**
     * 批量取消拉黑用户
     * @access  public
     * @param   array   $openidList     用户OpenID列表
     * @return  array
     * @throws InvalidResponseException
     */
    public function batchUnBlack(array $openidList): array
    {
        return $this->request(Request::METHOD_POST, 'https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist', [], [
            'openid_list' => $openidList
        ]);
    }
}