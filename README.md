# Laravel9-Support-Ticket-Tutorial
An example of how to create a Support Ticket System in Laravel 9 using Breeze

Laravel 9 Support Ticket System

These instructions will help guide you in creating a Support Ticket System in Laravel 9.
This guide is for a system created on Windows 10.
I am using xampp as my environment for Apache and MariaDB. These must both be started whenever running the software.
I am using Brackets as my code editor.
I am using Cmder as my console emulator.

Create a new project using composer in the console emulator.
 composer create-project laravel/laravel SupportTicket

Once the project has been created, open the project in your code editor. Navigate to the .env file and change the Database details from laravel to the name of your database
 DB_DATABASE=SupportTicket



In the emulator, cd to your project directory.
To create the authentication features of the project, I am using Laravel Breeze. This needs to be installed with composer (in the project directory)

 composer require laravel/breeze —dev

Once Breeze is installed, the blades need to be installed as part of breeze using php artisan
 php artisan breeze:install blade


Once this is done, a basic piece of software is available to run.

In a new emulator session, start the local server using php artisan from the project directory.

 cd SupportTicket
 php artisan serve

If the command is successful, you will see the following message:

 

Copy and paste the url into your browser to run the basic software.

In another new emulator session (there should be 3 now), start the Vite development server for the project. This will automatically recompile the code (including CSS) and refresh the browser each time code is saved in the project.

 cd SupportTicket
 npm run dev


Both the above sessions MUST BE LEFT RUNNING in order for the environment to exist. Do not close the emulator windows running the local server or the Vite development server. These windows can be minimised to run in the background.
Create Migrations, Controllers and Models
Next, create and update the migrations for the tables that will be required in the system.

For my system, I needed to create the following tables so I needed migrations for each.
·	Categories
·	Comments
·	Statuses
·	Tickets

I chose to create the controllers, models and migrations together in a single command. This method ensures that the controllers, models and migrations have similar names. 
To create the models and migrations (use the initial emulator session)
 php artisan make:model -mrc Category
 php artisan make:model -mrc Comment
 php artisan make:model -mrc Status
 php artisan make:model -mrc Ticket

Each command will take a few minutes to run.
Once the models and migrations have been created, open the project in the code editor.

In app/Models, the models for each will appear.

In database/migrations the new migrations will appear.
We need to edit the new migrations as well as the user table migration.

The migrations will be edited as follows:

......._create_categories_table.php:
Replace the existing public function up()

With:
public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

......._create_comments_table.php:
Replace the existing public function up()

With:
public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('comment');
            $table->timestamps();
        });
    }



......._create_statuses_table.php:
Replace the existing public function up()

With:
 public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

......._create_tickets_table.php:
Replace the existing public function up()

With:
public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('category_id')->unsigned();
            $table->string('title');
            $table->string('priority');
            $table->text('message');
            $table->string('status');
            $table->timestamps();
        });
    }


Edit the user table migration to add an is_admin fields to the table....
......._create_users_table.php:

$table->integer('is_admin')->unsigned()->default(0);
The new public function up() will look like this:
public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('is_admin')->unsigned()->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

Switch back to the emulator and run the migrations:

 php artisan migrate

This will create the tables with the fields that have been specified.
Set the routes for the Pages.
Routing needs to be set up so that the system knows where to “send” the system to. To do this we need to create routes in routes/web.php
Remember that there will be specific routes for Admin users and non-Admin users for tickets so there will need to be specific routes for these options.

From installing Breeze, there will be 2 routes that have already been created as well as a middleware route.
Routes for all Ticket functions, all Comment functions, categories and statuses need to be created.

Open routes/web.php in the code editor and add the includes for the controllers that will be added as routes.
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\StatusController;

Now add these routes after the middleware.
(Each route will be explained as it is reached.)

Route::resource('tickets', TicketController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::resource('comments', CommentController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::resource('categories', CategoryController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::resource('statuses', StatusController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::get('my_tickets', [TicketController::class,'userTickets'])->name('my_tickets');
Route::get('new_ticket', [App\Http\Controllers\TicketController::class,'create'])->name('new_ticket');
Route::post('new_ticket', [App\Http\Controllers\TicketController::class,'store']);

Route::get('tickets/{ticket_id}', [App\Http\Controllers\TicketController::class, 'show']);
Route::post('comment', [App\Http\Controllers\CommentController::class, 'postComment']);
Route::post('admincomment', [App\Http\Controllers\CommentController::class, 'postAdminComment']);

Route::get('admin_tickets', [App\Http\Controllers\TicketController::class, 'adminTickets']);
Route::post('ticket_status/{ticket_id}', [App\Http\Controllers\TicketController::class, 'status']);

To check the routes, switch to the emulator and use
php artisan route:list
This will show all of the routes that have been created.
(Only use this once the Controllers have been created).

Set the relationships.
In my system the relationships work  like this:

Categories can be used on MANY Tickets.
Comments BELONG TO a single Tickets.
Comments BELONG TO a single User.
Statuses can be used on MANY Tickets.
Users can have MANY tickets.
Users can have MANY comments.

To achieve this, each model needs to be edited. While adding the relationships, mass assignment protection should be added to each model.
Mass Assignment Protect allows only certain fields on the database tables to be updated. This will prevent accidental editing of fields that should not be edited.
This is done by defining fields that are fillable. You can also block fields by defining these as guarded.

app/Models/Category.php

Under the namespace add:
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

Add this below 
use HasFactory;
protected $fillable = ['name'];
    
    // Relationship to ticket
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


app/Models/Comment.php

Under the namespace add:
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

Add this below 
use HasFactory;
protected $fillable = ['ticket_id', 'user_id', 'comment'];
    
    // Relationship to ticket
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }


app/Models/Status.php

Under the namespace add:
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

Add this below 
use HasFactory;
protected $fillable = ['name'];
    
    // Relationship to ticket
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
app/Models/Ticket.php

Under the namespace add:
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

Add this below 
use HasFactory;

protected $fillable = [
        'user_id', 'category_id', 'ticket_id', 'title', 'priority', 'message', 'status',
    ];
    
    // Set the relationships to User and Personal
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
       
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id', 'id');
    }
    
    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    public function status(): HasMany
    {
        return $this->hasMany(Status::class);
    }
    
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }




app/Models/User.php

Under the namespace add:
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

Add to the end of the model

// Relationship creation for one user to many tickets
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
    
    // Relationship to Personal Details
    public function personals(): HasMany
    {
        return $this->hasMany(Personal::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

Now that the routes and the relationships have been created, the CONTROLLERS can be created...
Setting up Controllers.

Controllers

The TicketController handles most of the logic for this system. Set this up first.
app/Http/Controllers/TicketController.php

Add the following use statements to the top of TicketController
 use App\Models\Ticket;
use App\Models\Category;
use App\Models\Status;

Now add these to class TicketController extends Controller

public function index(): View
    {
        $categories = Category::all();
        $tickets = Ticket::with('user')->latest()->paginate(10);
        return view('tickets.index', compact('tickets'), compact('categories'));
    }
    
    public function create(): View  
    {
        $tickets = Ticket::with('user')->latest()->get();
        $categories = Category::all();
        return view('tickets.create', compact('tickets'), compact('categories'));
        
    }
    
    public function store(Request $request): RedirectResponse
    {
        $validated=$request->validate([
            'title' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'message' => 'required'
        ]);
        $ticket = new Ticket([
            'title' => $request->input('title'),
            'user_id' => auth()->user()->id,
            'category_id' => $request->input('category'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'status' => "Open"
        ]);
        $ticket->save();
        return redirect(route('tickets.index'));
    }
    
    public function userTickets()
    {
        // Set the relationships in Ticket and Category Models.
        // Then read using ::with and include user relationship and category relationship
        $tickets = Ticket::with('user', 'category')->where('user_id', auth()->user()->id)
            ->paginate(10);
        return view('tickets.user_tickets', compact('tickets'));
    }


    public function show($ticket_id): View
    {
        $ticket = Ticket::with('user', 'category','comment')->where('id', $ticket_id)->firstOrfail();
        $categories = Category::all();
        $statuses = Status::all();
        return view('tickets.show_ticket', compact('ticket'), compact('statuses'));
    }
    
    public function adminTickets()
    {
        // Set the relationships in Ticket and Category Models.
        // Then read using ::with and include user relationship and category relationship
        $tickets = Ticket::paginate(10);
        return view('admin.all_tickets', compact('tickets'));
    }

In addition to the TicketController, the CommentController will be used to update comments for each ticket.

In app/Http/Controllers/CommentController.php, below the namespace add:
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

Add the following into the CommentController class:
public function postComment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required'
        ]);
        $comment = Comment::create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id' => auth()->user()->id,
            'comment' => $request->input('comment')
        ]);        
        // Now read the ticket details and sent it back
        $ticket = Ticket::with('user', 'category','comment')->where('id', $request->input('ticket_id'))->firstOrfail();
        $statuses = Status::all();
        return view('tickets.show_ticket', compact('ticket'), compact('statuses'));
        
    }
    
    







 public function postAdminComment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'status' => 'required'
        ]);
        $comment = Comment::create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id' => auth()->user()->id,
            'comment' => $request->input('comment')
        ]);
        
        // Get the ticket being updated...
        $ticket = Ticket::where('id', $request->ticket_id)->firstOrFail();
        // Set the ticket status to the Admin selected ticket status
        $ticket->status = $request->status;
        // Save the details!
        $ticket->save();
        
        // Now read the ticket details and sent it back
        $ticket = Ticket::with('user', 'category','comment')->where('id', $request->input('ticket_id'))->firstOrfail();
        $statuses = Status::all();
        return view('tickets.show_ticket', compact('ticket'), compact('statuses'));
        
    }

The postComment is used by non-Admin users and the postAdminComment is used by the Admin user.
Creating Views.

The first view that the user will access is welcome.blade.php (resources/views).

The welcome blade will have the login and register options that is automatically provided by Breeze.
These can be found in resources/views/auth.
The user will need to register before being allowed to log in.

Once the user logs in, the software will route to the dashboard.
 Route::get('/dashboard', function () {
      return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

To allow for the user to navigate, the navigation.blade.php needs to be changed.
In resources/views/layouts/navigation.blade.php, add the following under the href for Dashboard.

   <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets')">
                        {{ __('Tickets') }}
                    </x-nav-link>
                </div>

This will direct the software to the custom views.

To create the custom views.
In resources/views, create a new FOLDER called tickets.
Create the following files in the new directory:
index.blade.php
create.blade.php
comments.blade.php
user_tickets.blade.php
show_ticket.blade.php
reply.blade.php

In resources/views, create a new FOLDER called admin.
Create the following files in the new directory:
all_tickets.blade.php
status.blade.php

The first view checks both user types. This will be resources/views/index.blade.php. This is accessed using the route:
 Route::resource('tickets', TicketController::class)
      ->only(['index', 'store', 'edit', 'update', 'destroy'])
      ->middleware(['auth', 'verified']);


Add the following code to resources/views/tickets/index.blade.php.

<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <table align="center">
            @if(auth()->user()->is_admin)
                <tr>
                    <td colspan="2">
                        <button class="button" onclick="location.href='{{ url('admin_tickets') }}'">All Tickets</button>
                    </td>
                </tr>
                    <!--See all <a href="{{ url('admin/tickets') }}">tickets</a>-->
            @else
                <tr>
                    <td>
                        <button class="button" onclick="location.href='{{ url('my_tickets') }}'">See all your tickets</button>  
                    </td>
                    <td>
                        <button class="button" onclick="location.href='{{ url('new_ticket') }}'">Open a new Ticket</button>
                    </td>
                </tr>
            @endif
        </table>
    </div>
</x-app-layout>

The if statement checks if the user logged in is an Admin type user or not.
If it is NOT an Admin, the code gives TWO options:
View All Tickets or Open a New Ticket.


Non Admin User
Since there are no tickets yet, start with the Open a New Ticket option.
This routes to resources/views/tickets/create.blade.php.
(See web.php => Route::get('new_ticket', [App\Http\Controllers\TicketController::class,'create'])->name('new_ticket');)
Add the following code to resources/views/create.blade.php

<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Open New Ticket</div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form class="form-horizontal" role="form" method="POST">
                            {!! csrf_field() !!}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Title</label>
                                <div class="col-md-6">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                <label for="category" class="col-md-4 control-label">Category</label>
                                <div class="col-md-6">
                                    <select id="category" type="category" class="form-control" name="category">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('category'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('category') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('priority') ? ' has-error' : '' }}">
                                <label for="priority" class="col-md-4 control-label">Priority</label>
                                <div class="col-md-6">
                                    <select id="priority" type="" class="form-control" name="priority">
                                        <option value="">Select Priority</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                    @if ($errors->has('priority'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('priority') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label for="message" class="col-md-4 control-label">Message</label>
                                <div class="col-md-6">
                                    <textarea rows="10" id="message" class="form-control" name="message"></textarea>
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
<button type="submit">
                                        <i class="fa fa-btn fa-ticket"></i> Open Ticket
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

This code does a few things.
The TicketController index class gets all of the tickets for the current user in descending order
 $tickets = Ticket::with('user')->latest()->get();
It also gets all of the categories.
 $categories = Category::all();
It returns these to the create blade.
 return view('tickets.create', compact('tickets'), compact('categories'));

The create.blade.php allows the user to capture a title for the ticket, select a category, select a priority and capture the details of the ticket.

To View All Tickets, the code routes to resources/views/tickets/user_ticket.blade.php
Add the following code to resources/views/tickets/user_ticket.blade.php

<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <div class="panel-heading">
            <i class="fa fa-ticket"> My Tickets</i>
        </div>
        <div class="panel-body">
            @if($tickets->isEmpty())
                <p>You have not created any tickets.</p>
            @else
                <table id="display">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)                        
                            <tr>
                                <td>
                                   {{$ticket->category->name}}
                                </td>
                                <td>
                                    <a href="{{ url('tickets/' . $ticket->id) }}">
                                        #{{ $ticket->id }} - {{ $ticket->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($ticket->status == "Open")
                                        <span class="label label-success">{{ $ticket->status }}</span>
                                    @else
                                        <span class="label label-danger">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $ticket->updated_at }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $tickets->render() }}
            @endif
        </div>
    </div>
</x-app-layout>

This links to the “See all your tickets” button on resources/views/tickets/index.blade.php.
The button is linked to the route my_tickets in routes/web.php which links back to app/Http/Controller/TicketController.php userTickets function.
This function gets the tickets for the current user as well as the category for the ticket and returns it to resources/views/tickets/user_tickets.blade.php.
Adding a Comment to a Ticket.
To select a ticket to add a comment, click on the Ticket Title on the “See all your tickets” page (user_tickets.blade.php)

In resources/views/tickets/show.blade.php, add the following:
<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    #{{ $ticket->id }} - {{ $ticket->title }}
                </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="ticket-info">
                        <p>{{ $ticket->message }}</p>
                        <p><b>Category: </b>{{ $ticket->category->name }}</p>
                        <p>
                            @if ($ticket->status === 'Open')
                            <b>Status:</b> <span class="label label-success">{{ $ticket->status }}</span>
                            @else
                            <b>Status:</b> <span class="label label-danger">{{ $ticket->status }}</span>
                            @endif
                        </p>
                        <p><b>Created on:</b> {{ $ticket->created_at->diffForHumans() }}</p>
                        <p><b>Created by:</b> {{ $ticket->user->name }}</p>
                    </div>
                </div>
            </div>
            <hr>
            @include('tickets.comments')
            <hr>
            @include('tickets.reply')
        </div>
    </div>
</x-app-layout>

This has code to include TWO move blades – comments.blade.php and reply.blade.php. 

In resources/views/tickets/comments.blade.php, add the following:
<div class="comments">
    @if(isset($ticket->comment))
        @foreach($ticket->comment as $comments)
            <div class="panel panel-default">
                <div class="panel panel-@if($ticket->user->id === $comments->user_id){{"default"}}@else{{"success"}}@endif">
                    <div class="panel panel-heading">
                        <span class="pull-right"><i><b>{{$comments->user->name}}</b> : {{ $comments->created_at }}</i></span>
                    </div>
                    <div class="panel panel-body">
                        {{ $comments->comment }}
                    </div>
                </div>
            </div>
            <hr>
        @endforeach
    @endif
</div>

This allows the user to view all comments – including the original ticket details – underneath the ticket heading information.


In resources/views/tickets/reply.blade.php add the following:
<div class="panel panel-default">
    <div class="panel-heading">Add reply</div>
    <div class="panel-body">
        <div class="comment-form">
            @if(auth()->user()->is_admin == 1)
                <form action="{{ url('admincomment') }}" method="POST" class="form">
            @else                    
                <form action="{{ url('comment') }}" method="POST" class="form">
            @endif
                {!! csrf_field() !!}
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
                    <textarea rows="10" id="comment" class="form-control" name="comment"></textarea>
                    @if ($errors->has('comment'))
                        <span class="help-block">
                           <strong>{{ $errors->first('comment') }}</strong>
                        </span>
                    @endif
                </div>
                @if(auth()->user()->is_admin)
                    <div class="form-group">
                        <hr>
                        @include('admin.status')
                        <br>
                </div>
                @endif
                <div class="form-group">
                    <button type="submit" class="button">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

Admin User Views
For an Admin user type, resources/views/tickets/index.blade.php will only give an option to view All Tickets. This will use the route
Route::get('admin_tickets', [App\Http\Controllers\TicketController::class, 'adminTickets']);
This will point to resources/views/admin/all_tickets.blade.php.

Add the following to resources/views/admin/all_tickets.blade.php:
<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <div class="panel-heading">
            <i class="fa fa-ticket"> All Tickets</i>
        </div>
        <div class="panel-body">
            @if($tickets->isEmpty())
                <p>You have not created any tickets.</p>
            @else
                <table id="display">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)                        
                            <tr>
                                <td>
                                   {{$ticket->category->name}}
                                </td>
                                <td>
                                    <a href="{{ url('tickets/' . $ticket->id) }}">
                                        #{{ $ticket->id }} - {{ $ticket->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($ticket->status == "Open")
                                        <span class="label label-success">{{ $ticket->status }}</span>
                                    @else
                                        <span class="label label-danger">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $ticket->updated_at }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $tickets->render() }}
            @endif
        </div>
    </div>
</x-app-layout>
This will show all of the tickets that have been captured.
To select a ticket to add a comment, click on the Ticket Title on the “All Tickets” page.
The link will direct to resources/views/tickets/show.blade.php
The resources/views/tickets/reply.blade.php checks if the user is an Admin. If the user IS an Admin, reply.blade.php allows for the ticket status to be changed. This will happen in resources/views/admin/status.blade.php.

In resources/views/admin/status.blade.php add the following:
<div class="panel panel-default">
    <div class="panel-body">
        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
            <label for="category" class="col-md-4 control-label">Status</label>
            <div class="col-md-6">
                <select id="status" type="status" class="form-control" name="status">
                    <option value="">Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->name }}" @if ($ticket->status == $status->name) selected @endif >
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('category'))
                    <span class="help-block">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

Bootstrap CSS for system and changing the system Logo.

To style the website so that the links, buttons and tables are more visible. To do this, edit resources/css/app.css and add the following BEFORE all @tailwind includes.

.button{
    background-color:darkgray !important;
    padding: 5px !important;
    border-radius: 9px !important;
}

#display {
  font-family: Arial, Helvetica, sans-serif !important;
  border-collapse: collapse !important;
}

#display td, #display th {
  border: 1px solid #ddd !important;
  padding: 8px !important;
}

#display tr:nth-child(even){
    background-color: #f2f2f2 !important;
}

#display tr:hover {
    background-color: #ddd !important;
}

#display th {
  padding-top: 12px !important;
  padding-bottom: 12px !important;
  background-color:darkgray !important;
  color: white !important;
}

a{
    color: darkblue !important;
}

The # before function denotes that the item is an id.
The . before the function denotes that the item is a class.
Nothing before the function name denotes that it will automatically apply to all instances of that name in the code.


Tables that require borders will be defined as:
<table id="display">

Buttons will be defined as:
<button class="button"></button>

All anchor tags will display in dark blue to make them stand out from other text.


To change the logos that appear from the defaults that were loaded by Breeze.
In the public folder, create an images folder. Store your logos in this folder.
For the Landing Page.
Edit resources/views/welcome.blade.php.
After the first IF statement - @if (Route::has('login')) - remove the entire svg tag as this is coded to display Laravel and the Laravel logo.
Replace this with an img tag to your new logo:
<img src="{{url('/images/full_logo.png')}}" height="100%" width="100%" alt="Image"/> 

To remove the blocks and other details created by Breeze, remove from the next
<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg"> until the last div for 
Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
To change the Application Logo
In resources/views/components/application.blade.php
Remove the entire <svg> tag.
Replace this with an <img> tag to your chosen logo:
<img src="{{url('/images/Logo.png')}}" height="100%" width="100%" alt="Image"/>
