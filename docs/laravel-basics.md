# Laravel Form Basics

## Creating a form blueprint

The first thing you need to do is create a form blueprint. See the [form blueprints](form-blueprints.md) page for more info.

## Using the FormBuilder to build the form

You will need to build your form in a controller. To do this you will need `AV\LaravelForm\FormBuilder`, but it's easy to get access to. Just type-hint it on your controller method and it will be automatically injected by Laravel

    use Form;

    // within your controller
    public function myControllerMethod(Request $request) 
    {
        $blueprint = new MyForm();
        
        $form = Form::build($blueprint);
    }
    
After building your form, you are given an instance of `AV\Form\FormHandler`

## Create the view and render the form

To get a form ready to be rendered you need to call `createView()` on your built form. It's easiest to do this right at the point you pass it to the view

    use Form;

    // within your controller
    public function myControllerMethod(Request $request) 
    {
        $blueprint = new MyForm();
        
        $form = Form::build($blueprint);
        
        return view('my_template')->with('form', $form->createView());
    }

In your template you will need to render the form. There are a few options that can give you a lot of control over how you display your form, but for now we are going to display the entire form in one go using the `form()` helper function.

If you are using blade, you can render your form like so:

    {!! form($form) !!}
    
Or if you are using plain PHP templates:

    <?php echo form($form);?>
    
You should now see your form rendered, but it won't do much yet.

## Checking if the form was submitted & valid

To check if the form was submitted, the request must be passed to the form handler. You can do this by using the second argument of the `FormBuilder build()` method or by calling `handleRequest()` on the built form.

You can then check to see if the form was submitted using the `isSubmitted()` and `isValid()` methods on the built form.

### One-page form submission

The simplest controller flow looks like this when a form submits to the same place it is displayed

    public function myControllerMethod(Request $request) 
    {
        $blueprint = new MyForm();
        
        $form = Form::build($blueprint, $request);
        
        // form was submitted & valid
        if ($form->isValid()) {
            $data = $form->getData();
            
            // do something with that data from the form
        }
        
        return view('my_template')->with('form', $form->createView());
    }

### Submit & Redirect

But you may want to redirect users after the form is submitted to avoid accidental resubmissions of forms:

    public function myControllerMethod(Request $request) 
    {
        $blueprint = new MyForm();
        
        $form = Form::build($blueprint, $request);
        
        // form was valid
        if ($form->isValid()) {
            $data = $form->getData();
            
            // do something with that data from the form
            
            return redirect('/some/other/page');
        }
                
        // check if the form was submitted. if yes, redirect the user back as the form was not valid
        if ($form->isSubmitted()) {
            return back();
        }
        
        // otherwise, just render the page that shows the form
        return view('my_template')->with('form', $form->createView());
    }

If you redirect right after a form submission, the form will be automatically repopulated with the submitted data. If you don't want to restore the submitted data to the form after the redirect, call `$form->cancelRestore()` before you redirect.

## Binding a model

Before binding a model to a form, you must make sure any parameters you want the form to save are set as [fillable](http://laravel.com/docs/5.1/eloquent#mass-assignment) on your eloquent model. The fields must be in the fillable array rather than the inverse using the guarded property.

To bind a model to a form, you can pass it as the 3rd parameter of the FormBuilder `build()` method or use the `bindEntity()` method on your built form.

    public function myControllerMethod(Request $request) 
    {
        // Get a 'car'
        $car = Car::find(1);
            
        // Bind it using the 3rd parameter of the build method
        $form = Form::build(new MyForm(), $request, $car);
        
        // ALTERNATIVELY bind the model after the form has been built
        // $form->bindEntity($car);
        
You can then save the form values to the model by calling `saveToEntities()` on your built form. This only assignes the form values to the model, so remember to then call `save()` on your model to save it's data to the database.
        
        if ($form->isValid()) {
            $form->saveToEntities();
            
            $car->save();
            
            return redirect('/some/other/page');
        }
        
        return view('my_template')->with('form', $form->createView());
    }
