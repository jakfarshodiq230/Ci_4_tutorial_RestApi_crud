<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
class c_pengeluaran extends ResourceController
{
    use ResponseTrait;
    protected $format       = 'json';
    protected $modelName    = 'App\Models\m_pengeluaran';
 
    public function index()
    {
        // return $this->respond($this->m_resetapi_pemasukan->all_person(), 200);
        $all = $this->model->where('jenis_transaksi', 'pengeluaran')->findAll();
        $response['status']=200;
        $response['error']=false;
        $response['person']=$all;
        return $this->respond($response,200);
    }

    public function create()
    {
        helper(['form']);

		$rules = [
			'rincian_transaksi' => 'required|min_length[6]',
            'jumlah' => 'required',
            'harga' => 'required',
			'struk_image' => 'uploaded[struk_image]|max_size[struk_image, 1024]|is_image[struk_image]'
		];

		if(!$this->validate($rules)){
			return $this->fail($this->validator->getErrors());
		}else{

			//Get the file
			$file = $this->request->getFile('struk_image');
			if(! $file->isValid())
				return $this->fail($file->getErrorString());

			$file->move('./Pengeluaran');

			$data = [
                'id_transaksi' => $this->model->get_idotomatis(),
				'rincian_transaksi' => $this->request->getVar('rincian_transaksi'),
                'jumlah' => $this->request->getVar('jumlah'),
                'harga' => $this->request->getVar('harga'),
                'total' => $this->request->getVar('jumlah') *  $this->request->getVar('harga'),
                'jenis_transaksi' => 'pengeluaran',
				'struk' => $file->getName()
			];

			$post_id = $this->model->insert($data);
			$data[$this->model->get_idotomatis()] = $post_id;
			return $this->respondCreated($data);
		}
    }


    public function show($id = null){
		$data = $this->model->find($id);
		return $this->respond($data);
    }
    
    public function update($id = null){
		helper(['form', 'array']);

		$rules = [
			'rincian_transaksi' => 'required|min_length[6]',
            'jumlah' => 'required',
            'harga' => 'required',
		];


		$fileName = dot_array_search('struk_image.name', $_FILES);

		if($fileName != ''){
			$img = ['struk_image' => 'uploaded[struk_image]|max_size[struk_image, 1024]|is_image[struk_image]'];
			$rules = array_merge($rules, $img);
		}



		if(!$this->validate($rules)){
			return $this->fail($this->validator->getErrors());
		}else{
			
			$data = [
				'id_transaksi' => $id,
				'rincian_transaksi' => $this->request->getVar('rincian_transaksi'),
                'jumlah' => $this->request->getVar('jumlah'),
                'harga' => $this->request->getVar('harga'),
                'total' => $this->request->getVar('jumlah') *  $this->request->getVar('harga'),
			];

			if($fileName != ''){

				$file = $this->request->getFile('struk_image');
				if(! $file->isValid())
					return $this->fail($file->getErrorString());

				$file->move('./Pengeluaran');
				$data['struk'] = $file->getName();
			}

			$this->model->save($data);
			return $this->respond($data);
		}

	}

	public function delete($id = null){
		$data = $this->model->find($id);
		if($data){
			$this->model->delete($id);
			return $this->respondDeleted($data);
		}else{
			return $this->failNotFound('Item not found');
		}
	}

}