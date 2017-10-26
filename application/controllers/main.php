<?php
header('Access-Control-Allow-Origin', '*');
error_reporting(0);
/**
 *
 */
class Main extends CI_Controller
{

  function __construct()
  {
  parent::__construct();
  $this->load->library('session');
  $this->load->database();
  $this->load->model('main_model','mm');
  $this->load->helper('url', 'form');
  }

  public function create_link(){
  $data = file_get_contents("php://input"); // accept json
  $user_data = json_decode($data, true); // decode json
  $user_data['name'] = str_replace("%20"," ",$user_data['name']);
  $username = $user_data['name'].rand(999,9999);
  
  $insertData = array(
    'user_name' =>$username,
    'name'=>$user_data['name'],
    'type'=>'user'
  );
  $response = $this->mm->insert_user($insertData);
  $this->session->set_userdata('id',$response);
  $this->session->set_userdata('username',$username);
  $this->session->set_userdata('name',$name);
  $user_data['name'] = str_replace("","%20",$user_data['name']);
  $username = str_replace("","%20",$username);
  $link  = "http://www.judgemeyar.tk/judge_me/index.php/main/quiz/".$username."/".$user_data['name'];
  $this->mm->send_response(true,"Success",$link);
  }

   public function set_session(){
  $data = file_get_contents("php://input"); // accept json
  $user_data = json_decode($data, true); // decode json
  $user_data['name'] = str_replace("%20"," ",$user_data['name']);
  $username = $user_data['name'].rand(999,9999);
  $insertData = array(
    'user_name' =>$username,
    'name'=>$user_data['name'],
    'type'=>'friend'
  );
  $response = $this->mm->insert_user($insertData);
  $this->mm->send_response(true,"Success",$response);
  }


  public function is_logged_in()
  {
    $is_logged_in=$this->session->all_userdata();
    if($is_logged_in['id']){
      $this->mm->send_response(true,"Success",$is_logged_in['name']);
    }
    else{
      $this->mm->send_response(true,"Failure",null);
    }
  }


  public function is_form_submited(){
    $is_logged_in=$this->session->all_userdata();
    if($is_logged_in['submit']){
      $this->mm->send_response(true,"Success",null);
    }
    else{
      $this->mm->send_response(true,"Failure",null);
    }
  }

  public function logout()
  {
    $this->session->sess_destroy();
    $this->mm->send_response(true,"Success",null);
  }



  public function list_questions($offset){
    $count = 0;
    $where  = array('status'=>1);
    $limit=1;
    $is_logged_in=$this->session->all_userdata();
    $Qresponse = $this->mm->list_questions($where,$limit,$offset);
    $where  = array('status'=>1,'qid'=>$Qresponse[0]['id']);
    $Oresponse = $this->mm->list_options($where);
    $where  = array('user_id'=>$is_logged_in['id'],'q_id'=>$Qresponse[0]['id']);
    $Aresponse = $this->mm->list_answers($where);
    $response['question']=$Qresponse[0];
    $response['option']=$Oresponse;
    $response['answer']=$Aresponse;
    $this->mm->send_response(true,"Success",$response);

  }

  public function save_response(){
  $is_logged_in=$this->session->all_userdata();
  $data = json_decode(file_get_contents("php://input"),true); // accept json
  $user_id = $is_logged_in['id'];
    $insert_data= array(
      'user_id'=> $user_id ,
      'friend_id'=> $data['fid'],
      'question_id'=> $data['qid'],
      'answer_id'=> $data['aid'],
      'result'=>$data['result']

    );
   $this->mm->save_response($insert_data);
    $this->mm->send_response(true,"Success",nul);
  }

    public function save_your_response(){
  $is_logged_in=$this->session->all_userdata();
  $data = json_decode(file_get_contents("php://input"),true); // accept json
  $user_id = $is_logged_in['id'];
    $insert_data= array(
      'user_id'=> $user_id ,
      'q_id'=> $data['qid'],
      'a_id'=> $data['aid'],

    );
   $this->mm->save_your_response($insert_data);
    $this->mm->send_response(true,"Success",nul);
  }

public function quiz($username,$name){
  $where = array('user_name'=>$username);
  $data = $this->mm->select_user($where);
  $user_id = $data[0]['id'];
  $this->session->set_userdata('id',$user_id);
  $this->session->set_userdata('username',$username);
  $this->session->set_userdata('name',$name);
  header('Location:http://www.judgemeyar.tk/judgeMe/#!');
  }


  public function list_response(){
    $is_logged_in=$this->session->all_userdata();
    $user_id = $is_logged_in['id'];
    if($user_id)
    $data2 = $this->mm->select_response($user_id);
    for($i= 0 ;$i<count($data2);$i++){
      $data3  = $this->mm->count_result($user_id,$data2[$i]['friend_id']);
      $response[$i]['friend'] = $data2[$i];
      $response[$i]['count'] = $data3;
    }
      $this->mm->send_response(true,"Success",$response);
  
  }

  public function single_response($fid){
  $is_logged_in=$this->session->all_userdata();
  $data = json_decode(file_get_contents("php://input"),true); // accept json
  $uid = $is_logged_in['id'];
  $data = $this->mm->select_all_response($uid,$fid);
   $this->mm->send_response(true,"Success",$data);
  }

}

 ?>
