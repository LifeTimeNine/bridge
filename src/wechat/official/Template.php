<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\official;

/**
 * 公众号模板消息管理
 */
class Template extends Basic
{
    /**
     * 设置所属行业
     * @access  public
     * @param   string  $industryId1    行业编号1
     * @param   string  $industryId2    行业编号2
     * @return  array
     * @throws InvalidResponseException
     */
    public function setIndustry(string $industryId1, string $industryId2): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=ACCESS_TOKEN', [], [
            'industry_id1' => $industryId1,
            'industry_id2' => $industryId2
        ]);
    }

    /**
     * 获取所属行业
     * @access  public
     * @return  array
     * @throws InvalidResponseException
     */
    public function getIndustry(): array
    {
        return $this->request('GET', 'https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=ACCESS_TOKEN');
    }

    /**
     * 添加模板
     * @access  public
     * @param   string      $templateId         板库中模板的编号
     * @param   array       $keywordNameList    选用的类目模板的关键词
     * @return  array
     * @throws InvalidResponseException
     */
    public function addTemplate(string $templateId, array $keywordNameList): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=ACCESS_TOKEN', [], [
            'template_id_short' => $templateId,
            'keyword_name_list' => $keywordNameList
        ]);
    }

    /**
     * 获取模板列表
     * @access  public
     * @return array
     * @throws InvalidResponseException
     */
    public function getAllPrivateTemplate()
    {
        return $this->request('GET', 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=ACCESS_TOKEN');
    }

    /**
     * 删除模板
     * @access  public
     * @param   string      $templateId         模板编号
     * @return  array
     * @throws InvalidResponseException
     */
    public function deletePrivateTemplate(string $templateId): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=ACCESS_TOKEN', [], [
            'template_id' => $templateId
        ]);
    }

    /**
     * 发送模板消息
     * @access  public
     * @param   string  $toUser         接收着OpenID
     * @param   string  $templateId     模板编号
     * @param   array   $data           数据['keyword1'=>['value'=>'xxx'],'keyword2'=>['value'=>'xxx']]
     * @param   string  $url            跳转连接
     * @param   array   $miniProgram    小程序所需参数['appid' => 'xxx','pagepath' => '']
     * @param   string  $clientMsgId    防重入ID
     * @return  array
     * @throws InvalidResponseException
     */
    public function send(string $toUser, string $templateId, array $data, string $url = null, array $miniProgram = [], string $clientMsgId = null): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN', [], [
            'touser' => $toUser,
            'template_id' => $templateId,
            'url' => $url,
            'miniprogram' => $miniProgram,
            'client_msg_id' => $clientMsgId,
            'data' => $data
        ]);
    }
}
