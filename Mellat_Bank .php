<?php

namespace idehpardazanjavan;

/**
 * This is just an example.
 */
class Mellat_Bank extends \yii\base\Widget
{
    public function pay($amount,$TerminalId,$UserName,$Password)
    {
        $client=new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wdl');
        $namespace='https://interfaces.core.sw.bps.com/';
        $error=$client->getError();
        if ($error)
        {
            return false;
        }
        $parameters=array(
            'terminalId'=>$TerminalId,
            'userName'=>$UserName,
            'userPassword'=>$Password,
            'orderId'=>time(),
            'amount'=>$amount,
            'localDate'=>date("ymd"),
            'localTime'=>date("His"),
            'additionalDate'=>'خرید',
            'callBackUrl'=> 'http://idehpardazanjavan.com/project/shop/order',
            'payerId'=>0

        );
        $result=$client->call('bpPayRequest',$parameters,$namespace);
        $res=@explode(',',$result);
        if (Sizeof($res)==2)
        {
            if ($res[0]==0)
            {
                return $res[1];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function verify($SaleOrderId,$SaleRefrenceId,$TerminalId,$UserName,$Password)
    {
        $client=new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wdl');
        $namespace='https://interfaces.core.sw.bps.com/';
        $error=$client->getError();
        if ($error)
        {
            return false;
        }
        $parameters=array(
            'terminalId'=>$TerminalId,
            'userName'=>$UserName,
            'userPassword'=>$Password,
            'orderId'=>$SaleOrderId,
            'saleOrderId'=>$SaleOrderId,
            'saleRefernceId'=>$SaleRefrenceId,
            );
        $VerifyAnswer =$client->call('bpVerifyRequest',$parameters,$namespace);
        if ($VerifyAnswer==0)
        {
            $result=$client->call('bpSettleRequest',$parameters,$namespace);
            return true;
        }
        else
        {
            $this->Inquiry($SaleOrderId,$SaleRefrenceId);
        }
            }
            public function Inquiry($SaleOrderId,$SaleReferenceId,$TerminalId,$UserName,$Password)
            {
                $client=new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wdl');
                $namespace='https://interfaces.core.sw.bps.com/';
                $error=$client->getError();
                if ($error)
                {
                    return false;
                }
                $parameters=array(
                    'terminalId'=>$TerminalId,
                    'userName'=>$UserName,
                    'userPassword'=>$Password,
                    'orderId'=>$SaleOrderId,
                    'saleOrderId'=>$SaleOrderId,
                    'saleRefernceId'=>$SaleRefrenceId,
                );
                $Inquiry=$client->call('bpInquiryRequest',$parameters,$namespace);
                if ($Inquiry==0)
                {
                    $result=$client->call('bpSettleRequest',$parameters,$namespace);
                    return true;
                }
                else
                {
                    $result=$client->call('bpReversalRequest',$parameters,$namespace);
                    return false;
                }
            }
}
