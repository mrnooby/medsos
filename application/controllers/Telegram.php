<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		
		$this->asd = $this->session->userdata('key');

		if(!$this->asd){
			redirect(LOGIN_PAGE);
		}
	}


	public function index()
	{
		$data['channel'] = $this->MTelegram->GetChannel();
		$data['title'] = "Telegram Dashboard";
		$data['isi'] = "telegram/dashboard";
		$this->load->view('theme',$data);		
	}

	//load table setting telegram
	public function loadTable()
	{
		$this->load->view("telegram/settingTable");
	}

	//query insert and update setting telegram
	public function add()
	{
		if($this->input->post('idChannel') == '') {
				$data = array(
						'channel_name' 		=> $this->input->post('namaChannel'),
						'chat_id' 			=> $this->input->post('channelID'),
						'api_token' 		=> $this->input->post('apiToken'),

				);

				$this->db->insert('telegram_channel', $data);
		}else{
				$data = array(
						'channel_name' 		=> $this->input->post('namaChannel'),
						'chat_id' 			=> $this->input->post('channelID'),
						'api_token' 		=> $this->input->post('apiToken'),

				);

			$this->db->where('id_channel', $this->input->post('idChannel'))->update('telegram_channel', $data); 			
		}
	}

	//get value telegram channel
	public function get()
	{
		if(isset($_POST["idChannel"]))  
			{  
				$getChannel = $_POST["idChannel"];
				$query = $this->db->query("SELECT *
											FROM 
												telegram_channel
											WHERE 
												id_channel = '$getChannel'");  
				$row = $query->row();
				echo json_encode($row);  
			} 
	}

	// delete setting telegram
	public function delete()
	{
		$id = $this->input->post('idChannel2');
		$this->db->delete('telegram_channel', array('id_channel' => $id)); 
	}

	//add and edit telegram proses
	public function addProses()
	{

		if($this->input->post('kontenCat')=='0'){
			$konten = $this->input->post('konten');
			$contenImage = "";
		}
		else{
			$konten = $this->input->post('captionImage');
			$contenImage = $this->input->post('imageSend');
		}	

		$idchan = $this->input->post('channelID');

		$row = $this->db->query("SELECT * FROM telegram_channel WHERE id_channel='$idchan'")->row();
		$apiToken = $row->api_token;

		if($this->input->post('period')=="2"){
			if($this->input->post('kontenCat')=="0"){
				$datar = [
				    'chat_id' 		=> $row->chat_id,
				    'text'			=> $konten,
				    'parse_mode'	=> 'html',
				];

				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($datar) );
			}else{
				$datar = [
					'chat_id' 		=> $row->chat_id,
				    'photo'			=> $contenImage,
				    'caption' 		=> $konten,
				    'parse_mode'	=> 'html',
				];

				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendPhoto?" . http_build_query($datar) );				
			}

			$status = "1";
			$sendSTatus = "1";
		}else{
			$status = "0";
			$sendSTatus = "0";			
		}

		if($this->input->post('idChannel') ==''){
			if($this->input->post('period')=="1"){
				$timeto24 = date("H:i", strtotime($this->input->post('timeSend1')));
				$data = array(
					'nama_proses'	=> $this->input->post('namaChannel'),
					'looping'		=> $this->input->post('period'),
					'channel_id'	=> $this->input->post('channelID'),
					'konten'		=> $konten,					
					'loopevery' 	=> $this->input->post('loopEvery'),
					'startdatetime' => $this->input->post('dateSend1')." ".$timeto24,
					'type_konten'	=> $this->input->post('kontenCat'),
					'image_konten'	=> $contenImage,
				);
			}else if($this->input->post('period')=="0"){
				$timeto24 = date("H:i", strtotime($this->input->post('timeSend')));
				$data = array(
					'nama_proses'	=> $this->input->post('namaChannel'),
					'looping'		=> $this->input->post('period'),
					'channel_id'	=> $this->input->post('channelID'),
					'konten'		=> $konten,					
					'startdatetime' => $this->input->post('dateSend')." ".$timeto24,
					'type_konten'	=> $this->input->post('kontenCat'),
					'image_konten'	=> $contenImage,				
				);				
			}else{
				$data = array(
					'nama_proses'	=> $this->input->post('namaChannel'),
					'channel_id'	=> $this->input->post('channelID'),
					'konten'		=> $konten,					
					'startdatetime' => date('Y-m-d H:i:s'),
					'type_konten'	=> $this->input->post('kontenCat'),
					'image_konten'	=> $contenImage,
					'send_status'	=> $sendSTatus,
					'status'		=> $status,					
				);						
			}

			$this->db->insert('telegram_proses', $data);
		}else{
			if($this->input->post('period')=="1"){
				$timeto24 = date("H:i", strtotime($this->input->post('timeSend1')));
				$dateTime = $this->input->post('dateSend1')." ".$timeto24;
				if((strtotime($dateTime))>(strtotime(date("Y-m-d H:i:s")))){
					$status = "0";
				}else{
					$status = "1";
				}				
				$data = array(
					'nama_proses'	=> $this->input->post('namaChannel'),
					'looping'		=> $this->input->post('period'),
					'channel_id'	=> $this->input->post('channelID'),
					'konten'		=> $konten,					
					'loopevery' 	=> $this->input->post('loopEvery'),
					'startdatetime' => $this->input->post('dateSend1')." ".$timeto24,
					'send_status'	=> '0',
					'status'		=> $status,
					'type_konten'	=> $this->input->post('kontenCat'),
					'image_konten'	=> $contenImage,
				);
			}else if($this->input->post('period')=="0"){
				$timeto24 = date("H:i", strtotime($this->input->post('timeSend')));
				$dateTime = $this->input->post('dateSend')." ".$timeto24;
				if((strtotime($dateTime))>(strtotime(date("Y-m-d H:i:s")))){
					$status = "0";
				}else{
					$status = "1";
				}					
				$data = array(
					'nama_proses'	=> $this->input->post('namaChannel'),
					'looping'		=> $this->input->post('period'),
					'channel_id'	=> $this->input->post('channelID'),
					'konten'		=> $konten,					
					'startdatetime' => $this->input->post('dateSend')." ".$timeto24,
					'send_status'	=> '0',
					'status'		=> $status,
					'type_konten'	=> $this->input->post('kontenCat'),
					'image_konten'	=> $contenImage,
				);				
			}else{
				$data = array(
					'nama_proses'	=> $this->input->post('namaChannel'),
					'looping'		=> $this->input->post('period'),
					'channel_id'	=> $this->input->post('channelID'),
					'konten'		=> $konten,					
					'startdatetime' => date('Y-m-d H:i:s'),
					'type_konten'	=> $this->input->post('kontenCat'),
					'image_konten'	=> $contenImage,
					'send_status'	=> $sendSTatus,
					'status'		=> $status,					
				);	

			}

			$this->db->where('id_telegram_proses', $this->input->post('idChannel'))->update('telegram_proses', $data); 				
		}
	}

	//load table non loop
	public function loadTableProsesNonLoop()
	{
		$this->load->view('telegram/prosesTableNonLoop');
	}

	//get value telegram proses
	public function getProses()
	{
		if(isset($_POST["idChannel"]))  
			{  
				$getChannel = $_POST["idChannel"];
				$query = $this->db->query("SELECT *,DATE(startdatetime) AS tanggal,TIME(startdatetime) AS jam
											FROM 
												telegram_proses
											WHERE 
												id_telegram_proses = '$getChannel'");  
				$row = $query->row();
				echo json_encode($row);  
			} 
	}

	public function confirmProses()
	{
		$id = $this->input->post('idDisableEnable');
		$query = $this->db->query("SELECT * FROM telegram_proses WHERE id_telegram_proses ='$id'")->row();
		if($query->status == "0"){
			$data = array(
				'status' => '1',
			);
		}else{
			$data = array(
				'status' => '0',
			);
		}
		$this->db->where('id_telegram_proses', $id)->update('telegram_proses', $data); 
	}

	//load table non loop done
	public function loadTableProsesNonLoopDone()
	{
		$this->load->view('telegram/prosesTableNonLoopDone');
	}

	//load table non loop
	public function loadTableProsesLoop()
	{
		$this->load->view('telegram/prosesTableLoop');
	}

	//load table non loop
	public function loadTableProsesLoopDone()
	{
		$this->load->view('telegram/prosesTableLoopDone');
	}				

	public function deleteProses()
	{
		$id = $this->input->post('idProses');
		$this->db->delete('telegram_proses', array('id_telegram_proses' => $id)); 
	}
}

/* End of file Telegram.php */
/* Location: ./application/controllers/Telegram.php */