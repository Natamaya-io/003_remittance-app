<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use PDO;

final class UserController extends BaseController
{
    public function index():void{$this->permission('user.manage');$users=App::db()->query('SELECT u.*,r.code role_code,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.name')->fetchAll(PDO::FETCH_ASSOC);$this->view('users/index',compact('users')+['title'=>'Manajemen User']);}
    public function create():void{$this->permission('user.manage');$roles=App::db()->query('SELECT * FROM roles ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);$this->view('users/form',['title'=>'Tambah User','user'=>null,'roles'=>$roles]);}
    public function store():void{$this->permission('user.manage');$data=$this->validated(true);$stmt=App::db()->prepare('INSERT INTO users(role_id,name,username,email,password,transaction_team,is_active,must_change_password,created_at,updated_at)VALUES(?,?,?,?,?,?,?,1,NOW(),NOW())');$stmt->execute([$data['role_id'],$data['name'],$data['username'],$data['email'],password_hash($data['password'],PASSWORD_DEFAULT),$data['transaction_team'],$data['is_active']]);$id=(int)App::db()->lastInsertId();AuditService::log('create','user',$id,null,array_diff_key($data,['password'=>true]));flash('success','User berhasil dibuat.');redirect('/users');}
    public function edit(string$id):void{$this->permission('user.manage');$user=$this->find((int)$id);$roles=App::db()->query('SELECT * FROM roles ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);$this->view('users/form',['title'=>'Ubah User','user'=>$user,'roles'=>$roles]);}
    public function update(string$id):void{$this->permission('user.manage');$before=$this->find((int)$id);$data=$this->validated(false);$params=[$data['role_id'],$data['name'],$data['username'],$data['email'],$data['transaction_team'],$data['is_active']];$sql='UPDATE users SET role_id=?,name=?,username=?,email=?,transaction_team=?,is_active=?';if($data['password']!==''){$sql.=',password=?,must_change_password=1';$params[]=password_hash($data['password'],PASSWORD_DEFAULT);}$sql.=',updated_at=NOW() WHERE id=?';$params[]=(int)$id;App::db()->prepare($sql)->execute($params);AuditService::log('update','user',(int)$id,$before,array_diff_key($data,['password'=>true]));flash('success','User diperbarui.');redirect('/users');}
    public function destroy(string$id):void{$this->permission('user.manage');if((int)$id===Auth::id()){flash('danger','Akun sendiri tidak dapat dihapus.');redirect('/users');}$before=$this->find((int)$id);App::db()->prepare('DELETE FROM users WHERE id=?')->execute([(int)$id]);AuditService::log('delete','user',(int)$id,$before,null);flash('success','User dihapus.');redirect('/users');}
    private function validated(bool$create):array{$name=trim((string)($_POST['name']??''));$username=trim((string)($_POST['username']??''));$email=trim((string)($_POST['email']??''));$password=(string)($_POST['password']??'');if($name===''||$username===''||$email===''||($create&&strlen($password)<8)){flash('danger','Nama, username, email, dan password minimal 8 karakter wajib diisi.');redirect($_SERVER['HTTP_REFERER']??'/users');}return['role_id'=>(int)($_POST['role_id']??0),'name'=>$name,'username'=>$username,'email'=>$email,'password'=>$password,'transaction_team'=>(int)($_POST['transaction_team']??0)?:null,'is_active'=>isset($_POST['is_active'])?1:0];}
    private function find(int$id):array{$s=App::db()->prepare('SELECT * FROM users WHERE id=?');$s->execute([$id]);$r=$s->fetch(PDO::FETCH_ASSOC);if(!$r)throw new \RuntimeException('User tidak ditemukan.');return$r;}
}
