<h1>Index</h1>

<p>
This is the main page welcome!

 <?php if(isset($this->msg))
    {
        echo $this->msg;   
    }
    ?>
 <?php if(isset($_POST['error']))
    {
        echo $_POST['error'];   
    }
    ?>
</p>
