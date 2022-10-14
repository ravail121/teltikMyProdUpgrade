<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Support\DataProviders\StatesProvider;

/**
 * Class CustomerPlanController
 *
 * @package App\Http\Controllers
 */
class CustomerPlanController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function get()
    {
        $states   = $this->states();
        $customer     = $this->requestConnectionForCustomer('customer');
        $slug = array_column($customer['company']['carrier'], 'slug');
        $subcriptions = $this->requestConnectionForCustomer('customer-subscriptions');
        
        if($subcriptions === 'Failed'){
            $subcriptions = null;
            $usages = null;
        }else{
            $usages = $this->getUsages($subcriptions['customer-plans']);
           
            if(!$usages){
                $usages = null;
            }
        }
        
        $daysUntilAutoPay = Carbon::today()->diffInDays(Carbon::parse($customer['billing_end'])->subDays(1));
        return view('customer.plan-details', compact('subcriptions', 'customer', 'states',
            'daysUntilAutoPay', 'usages', 'slug'));
    }

    public function test()
    {
        $states   = $this->states();
        $customer     = $this->requestConnectionForCustomer('customer');
        $slug = array_column($customer['company']['carrier'], 'slug');
        $subcriptions = $this->requestConnectionForCustomer('customer-subscriptions');
        
        if($subcriptions === 'Failed'){
            $subcriptions = null;
            $usages = null;
        }else{
            $usages = $this->getUsages2($subcriptions['customer-plans']);
           
            if(!$usages){
                $usages = null;
            }
        }
        
        $daysUntilAutoPay = Carbon::today()->diffInDays(Carbon::parse($customer['billing_end'])->subDays(1));
        return view('customer.plan-details', compact('subcriptions', 'customer', 'states',
            'daysUntilAutoPay', 'usages', 'slug'));
    }

    /**
     * @param $id
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function compatiblePlans($id)
    {
        $data['subscription_id'] = $id;
        $data['customer_id']     = session('id');
        $customer     = $this->requestConnectionForCustomer('customer');
        $compatiblePlans = $this->requestConnection('compatible-plans', 'get', $data);
        if(is_string ($compatiblePlans[0])){
            return $this->failRedirect('/customer-plans', $compatiblePlans[0]);
        }


        return view('customer.compatible-plans', compact('compatiblePlans','customer','id'));
    }

    /**
     * @param Request $request
     *
     * @return \App\Support\Utilities\Collection
     */
    public function compatibleAddons(Request $request)
    {
        $data = $request->all();
        $compatibleAddons = $this->requestConnection('compatible-addons', 'get', $data);

        return $compatibleAddons;
    }

    /**
     * @return \App\Support\DataProviders\Collection
     */
    private function states()
    {
        return StatesProvider::data();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function updatePort(Request $request)
    {
        $data =  $request->validate([
            'authorized_name'               => 'required|max:20',
            'address_line1'                 => 'required',
            'city'                          => 'required|max:20',
            'zip'                           => 'numeric|required',
            'state'                         => 'required',
            'number_to_port'                => 'numeric|required',
            'company_porting_from'          => 'required',
            'account_number_porting_from'   => 'required',
            'account_pin_porting_from'      => 'required',

        ]);
        $data['id'] = $request->id;
        $data['sim_card_number'] = $request->sim_card_number;
        $data['subscription_id'] = $request->subscription_id;
        $data['ssn_taxid'] = $request->ssn_taxid;
        $data['address_line2'] = $request->address_line2;
        $data['status'] = '1';

        $compatibleAddons = $this->requestConnection('update-port', 'post', $data);
        if($compatibleAddons[0] =='sucessfully Updated'){
            return $data;
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function changePlan(Request $request)
    {
        $data = $request->all();
        $data['paid_monthly_invoice'] = session('paid_monthly_invoice');
        if(!empty(session('cart')['order_groups'])){
            return $this->successRedirect('checkout?order_hash='.session('hash')['order_hash'], 'Please Complete your new Order then change existing Plan');
        }
        session()->forget(['cart', 'hash', 'checkout_done', 'status', 'couponAmount', 'couponCode']);
        $order = $this->requestConnectionForCustomer('change-plan', 'post', $data);

        if($order['status'] =='upgrade'){
            session(['changePlanStatus' => 'Upgrade']);

            return $this->successRedirect('checkout?order_hash='.$order['hash'].'&change_plan='.$data['active_plans'], 'Please confirm your upgrade request');
        }elseif ($order['status'] =='sameplan') {
            session(['changePlanStatus' => 'sameplan']);

            return $this->successRedirect('checkout?order_hash='.$order['hash'].'&change_plan='.$data['active_plans'], 'Please confirm your request');
        }else{
            session(['changePlanStatus' => 'Downgrade']);
            return $this->successRedirect('checkout?order_hash='.$order['hash'].'&change_plan='.$data['active_plans'], 'Please confirm your Change Plan request');
        }
    }

    /**
     * @param Request $request
     *
     * @return \App\Support\Utilities\Collection
     */
    public function updateSubLabel(Request $request)
    {
        $data['id'] = $request->id;
        $data['label'] = $request->label;

        $responses = $this->requestConnection('update-sub-label', 'post', $data);

        return $responses;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function checkNumber(Request $request)
    {
        $count = $this->requestConnectionForCustomer('check-number', 'get', [
            'number' => $request->number_to_port,
        ]);

        return ($count['count'] > 0 ) ? 'false' : 'true';
    }

    /**
     * Get Usage Data
     * @param $subscriptions
     *
     * @return array|\Illuminate\Support\Collection[]|\Psr\Http\Message\StreamInterface[]|string[]
     */
    private function getUsages($subscriptions)
    {
        $usages = [];
        $cycle_response = $this->requestUltraMobileConnection('cycles' , 'get');
        $current_cycle_id = null;
        foreach ($subscriptions as $subscription){

             $subscription_id = (int)$subscription['id'];
                $arr['sim_card_num']=$subscription['sim_card_num'];
                $usageData=$this->requestConnection('check2' , 'post', $arr);
                if(isset($usageData['data'])) {

                    $usages[$subscription_id]=[
                        'data'      => $usageData['data'],
                        'voice'     => $usageData['voice'],
                        'sms'      => $usageData['sms'],
                        'last_updated'=> $usageData['updated_at']
                    ];

                }

               
   
        }
        return $usages;
    }

    private function getUsages2($subscriptions)
    {
        $usages = [];
        $cycle_response = $this->requestUltraMobileConnection('cycles' , 'get');
        $current_cycle_id = null;
        foreach ($subscriptions as $subscription){

             $subscription_id = (int)$subscription['id'];
                $arr['sim_card_num']=$subscription['sim_card_num'];
                $usageData=$this->requestConnection('check2' , 'post', $arr);
                if(isset($usageData['data'])) {

                    $usages[$subscription_id]=[
                        'data'      => $usageData['data'],
                        'voice'     => $usageData['voice'],
                        'sms'      => $usageData['sms'],
                        'last_updated'=> $usageData['updated_at']
                    ];

                }

               
   
        }
        return $usages;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function downloadUsages(Request $request)
    {
        $url = bin2hex(implode(" ", $request->all()));
        return env('API_TOOL_URL').'/download-usages/'.$url;
    }

    /**
     * @param Request $request
     *
     * @return array|string
     */
    public function getUsagesData(Request $request)
    {
        
        $usageType = $request->get('usage_type');
        $offset  = $request->get('start');
        $limit  = $request->get('length');
        $date=$request->get('date');
        if($request->get('date')==null){
            $date='2022'.date('m');
        }

        if($request->get('cycle')){
            $date=$request->get('cycle');
        }

        if($usageType=='voice'){
            $usageType=1;
        }

        if($usageType=='data'){
            $usageType=3;
        }

        if($usageType=='text'){
            $usageType=2;
        }

        $parameters = [
            'sim'   => (int) $request->get('subscription_id'),
            'type' => $usageType,
            'date' => $date
        ];
        $usageData = $this->requestUltraMobileConnection('/getApi.php', 'post', $parameters);
        $outputUsages = [];
        $total=0;

        

        
        if ($usageData != "Failed" && isset($usageData)) {
            $usages = $usageData['data'];
            foreach ($usages as $usage) {

                if($usageType!=3){

                    $outputUsages[] = (object)[
                        'msisdn'          => $usage['msisdn'],
                        'local_time_call_started' => $usage['local_time_call_started'],
                        'ans_time_duration_min' =>$usage['ans_time_duration_min'],
                        'call_direction' => ($usage['call_direction']==0) ? "Outgoing" : "Incoming",
                        'translated_number' =>$usage['subscriber_call_no'],
                        'tech_used' =>$usage['tech_used'],

                        // 'time'          => Carbon::parse($usage['log_data'])->format('H:i:s'),
                        // $usageType      => number_format($usage[$usageType], 2)
                    ];
                    $total=$total+$usage['ans_time_duration_min'];
                    

                }else{
                    $outputUsages[] = (object)[
                        'msisdn'          => $usage['msisdn'],
                        'local_time_call_started' => $usage['local_time_call_started'],
                        'total_volume_mb' =>$usage['total_volume_mb'],
                        'tech_used' =>$usage['tech_used'],

                        // 'time'          => Carbon::parse($usage['log_data'])->format('H:i:s'),
                        // $usageType      => number_format($usage[$usageType], 2)
                    ];
                    $total=$total+$usage['total_volume_mb'];

                }
               
                    
                
            }
            
            
            if($request->has('download') && $request->download && $outputUsages) {
                /**
                 * @see https://bit.ly/2X3epC6
                 */
                $fileHandle = fopen( 'php://output', 'w' );
                ob_start();
                if($usageType!=3){

                    $header   = [
                        'msisdn',
                        'local_time_call_started',
                        'ans_time_duration_min',
                        'call_direction',
                        'translated_number',
                        'tech_used',
                        
                    ];

                }else{

                    $header   = [
                        'msisdn',
                        'local_time_call_started',
                        'total_volume_mb',
                        'tech_used',
                        
                    ];

                }
                

                // $cycleText = $request->cycle_text;

                // $cycleText = str_slug($cycleText, '-');

                fputcsv( $fileHandle, $header );
                foreach ($usages as $usage) {

                    if($usageType!=3){
    
                        

                        $rowData  = [
                            $usage['msisdn'],
                            $usage['local_time_call_started'],
                            $usage['ans_time_duration_min'],
                            ($usage['call_direction']==0) ? "Outgoing" : "Incoming",
                            $usage['subscriber_call_no'],
                            $usage['tech_used'],
                        ];
    
                        fputcsv( $fileHandle, $rowData );
                        
    
                    }else{
                        $outputUsages[] = (object)[
                            'msisdn'          => $usage['msisdn'],
                            'local_time_call_started' => $usage['local_time_call_started'],
                            'total_volume_mb' =>$usage['total_volume_mb'],
                            'tech_used' =>$usage['tech_used'],
    
                            // 'time'          => Carbon::parse($usage['log_data'])->format('H:i:s'),
                            // $usageType      => number_format($usage[$usageType], 2)
                        ];
                        $rowData  = [
                            $usage['msisdn'],
                            $usage['local_time_call_started'],
                            $usage['total_volume_mb'],
                            $usage['tech_used'],
                        ];
    
                        fputcsv( $fileHandle, $rowData );
    
                    }
                   
                        
                    
                }

                fclose( $fileHandle );

                $csvObject = ob_get_contents();

                ob_get_clean();

                $fileName = 'usage-'. $usageType . '-'. date('l') . '.csv';
                
                return \Response::make( $csvObject, 200, [
                    'Content-Type'              => 'application/octet-stream',
                    'Content-Description'       => 'File Transfer',
                    'Content-Disposition'       => 'attachment; filename="' . $fileName . '";',
                    'Pragma'                    => 'private',
                    'Expires'                   => 0,
                    'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
                    'Content-Transfer-Encoding' => 'binary'
                ] );
            }
            $_outputUsages = array_slice($outputUsages, $offset, $limit);

            return [
                'data'              => $_outputUsages,
                'recordsTotal'      => count($outputUsages),
                'recordsFiltered'   => count($outputUsages),
                'total'             => $total,
                'cycles'            =>$usageData['cycle']
            ];
        } else {
            return 'Failed';
        }
    }

    /**
     * @param Request $request
     *
     * @return \App\Support\Utilities\Collection
     */
    public function changeSim(Request $request)
    {
        $data = $request->all();
        $data['customer_id'] = session('id');

        return $this->requestConnection('change-sim' , 'post', $data);
    }

    /**
     * Update Requested ZIP while getting new number
     * @param Request $request
     *
     * @return \App\Support\Utilities\Collection
     */
    public function updateRequestedZip(Request $request)
    {
        $data['id'] = $request->id;
        $data['requested_zip'] = $request->requested_zip;

        $response = $this->requestConnection('subscription/update-requested-zip', 'post', $data);

        return $response;

    }
}


