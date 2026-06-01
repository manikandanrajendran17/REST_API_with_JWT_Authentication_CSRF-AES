<?php

require_once "../app/models/Patient.php";
require_once "../app/helpers/Response.php";
require_once "../app/helpers/AES.php";

class PatientController
{
    private $patientModel;
    public function __construct()
    {
        $this->patientModel = new Patient();
    }


    // GET Patients By User

    public function index()
    {
        $currentUser = AuthMiddleware::handle();
        $userId =$currentUser['user_id'];
        $patients =$this->patientModel->getByUser($userId);
  
     foreach ($patients as &$patient)
           {
            $patient['name'] = AES::decrypt($patient['name']);
            $patient['phone'] = AES::decrypt($patient['phone']);
            $patient['address'] = AES::decrypt($patient['address']);
          }

        Response::json(["status" => true, "data" => $patients]);
    }

    // POST Patient

    public function store($request)
    {
        $body =$request['body'];
        $name = $body['name']?? '';
        $age = $body['age']?? '';
        $gender = $body['gender']?? '';
        $phone = $body['phone']?? '';
        $address = $body['address'] ?? '';

        $name = AES::encrypt($name);
        $phone = AES::encrypt($phone);
        $address = AES::encrypt($address);


        if ( !$name || !$age) {
            Response::json(
            [  "message" =>"Required fields missing" ],400);
        }
        $currentUser = AuthMiddleware::handle();
        $userId = $currentUser['user_id'];

        $this->patientModel ->create([

            "name" => $name,
            "age" => $age,
            "gender" => $gender,
            "phone" => $phone,
            "address" => $address,
            "user_id" => $userId
        ]);


        Response::json(
        [
            "message" => "Patient created"],201);
    }

    // PUT Patient

        public function update( $id, $request)
      {

         $body = $request['body'];
         $currentUser = AuthMiddleware::handle();
         $userId = $currentUser['user_id'];
         $patient =$this->patientModel->getPatientByUser( $id, $userId);

       if(!$patient)

        {Response::json(["message" =>"Access denied"],403);}

        $updateData = [
         "name"    => AES::encrypt($body['name'] ?? ''),
         "age"     => $body['age'] ?? '',
         "gender"  => $body['gender'] ?? '',
         "phone"   => AES::encrypt($body['phone'] ?? ''),
         "address" => AES::encrypt($body['address'] ?? '')
    ];
        $this->patientModel->updatePatient($id,$updateData);

        Response::json([ "message" =>"Patient updated"]);
}

    // DELETE Patient

      public function delete($id)

         {$currentUser =AuthMiddleware::handle();

          $userId =$currentUser['user_id'];
          $patient =$this->patientModel->getPatientByUser($id,$userId);

           if(!$patient)

              {Response::json(["message" =>"Access denied"],403);}

            $this->patientModel->delete($id);
            Response::json(["message" =>"Patient deleted"]);
             }
}