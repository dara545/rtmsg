# Realtime app with Vue, Laravel, Socket.io and Redis
## Install Laravel
- \config\app.php
```
	'timezone' => 'Australia/Sydney',
```
- \app\Providers\AppServiceProvider.php
```
use Illuminate\Support\Facades\Schema;
    public function boot() {
        Schema::defaultStringLength(125);
    }
```

## Model & Migration
- php artisan make:model -m PrivateMessage
	> create model & migration
- \database\migrations\2018_01_04_044314_create_private_messages_table.php
```
	Schema::create('private_messages', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('sender_id')->unsigned();
		$table->integer('receiver_id')->unsigned();
		$table->string('subject');
		$table->text('message');
		$table->boolean('read');
		$table->timestamps();

		$table->index('sender_id');
		$table->index('receiver_id');
		$table->index(['sender_id', 'read']);
	});
```
- \app\PrivateMessage.php
```
use App\User;
use Carbon\Carbon;
...
    protected $fillable = ['sender_id', 'receiver_id', 'subject', 'message', 'read'];
    protected $appends = ['sender', 'receiver'];

	public function getCreatedAttribute($value) {
        return Carbon::parse($value)->diffForHumans();
    }
    public function getSenderAttribute(){
        return User::where('id', $this->sender_id)->first();
    }
    public function getReceiverAttribute() {
        return User::where('id', $this->receiver_id)->first();
    }
```
> While $fillable serves as a "white list" of attributes that should be mass assignable, you may also choose to use $guarded. The $guarded property should contain an array of attributes that you do not want to be mass assignable. All other attributes not in the array will be mass assignable. So, $guarded functions like a "black list". Of course, you should use either $fillable or $guarded - not both.
- \database\seeds\DatabaseSeeder.php
```
    public function run() {
        factory(App\User::class, 10)->create();
    }
```	
- php artisan migrate --seeds
- (php artisan db:seed)

- php artisan tinker 
```
App\PrivateMessage::create(['sender_id'=>1, 'receiver_id'=>2, 'subject'=>'How are you?', 'message'=>'Hey, how are you', 'read'=>0]); 
App\PrivateMessage::where('id', 1)->first();

```
- \routes\web.php
```
Route::get('test', function(){
    return App\PrivateMessage::where('id',1)->first();
});
```
- php artisan make:auth

## Controller
- php artisan make:controller PrivateMessageController
- \routes\web.php
	Route::post('get-private-message-notifications', 'PrivateMessageController@getUserNotifications');
	Route::post('get-private-messages', 'PrivateMessageController@getPrivateMessages');
	Route::post('get-private-message', 'PrivateMessageController@getPrivateMessageById');
	Route::post('get-private-messages-sent', 'PrivateMessageController@getPrivateMessageSent');
	Route::post('send-private-messages', 'PrivateMessageController@sendPrivateMessage');
