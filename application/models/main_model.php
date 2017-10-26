<?php

/**
*
*/
class Main_model extends CI_Model
{
    public function send_response($status,$msg,$data){
    $response['status']=$status;
    $response['msg']=$msg;
    $response['data']=$data;
    echo json_encode($response); // encode json
    die();

  }


  public function select_session($where){
  $query= $this->db->get_where('ci_sessions', $where)->result_array();
  return $query;
}


 public function select_user($where){
  $query= $this->db->get_where('user', $where)->result_array();
  return $query;
}

  public function list_questions($where,$limit,$offset){
    $this->db->limit($limit,$offset);
  $query= $this->db->get_where('questions',$where)->result_array();
  return $query;
}


 public function list_options($where){
  $query= $this->db->get_where('options',$where)->result_array();
  return $query;
}

 public function insert_user($insert_data){
 $this->db->insert('user', $insert_data);
 return $this->db->insert_id(); 
}

 public function save_response($insert_data){
 $this->db->insert('response', $insert_data); 
 }

 public function select_response($user_id){
 $SQL = "SELECT DISTINCT `friend_id`,`name` FROM response INNER JOIN user ON response.friend_id = user.id WHERE response.user_id =".$user_id." ORDER BY response.user_id DESC";
 $query = $this->db->query($SQL);
//  echo $this->db->last_query();
return $query->result_array(); 
 }
 


 public function select_all_response($uid,$fid){
 $SQL = "SELECT questions.question,options.option FROM response 
        INNER JOIN questions ON response.question_id = questions.id
        INNER JOIN options ON response.answer_id = options.id 
        WHERE response.user_id =".$uid." AND response.friend_id =".$fid."";
 $query = $this->db->query($SQL);
//  echo $this->db->last_query();
return $query->result_array(); 
 }
}

?>
