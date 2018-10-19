
<html>
     <div id ="container" style="width: 1350px">
      <div id="header" style="background-color: #3b5998">
       <h1 style="margin-bottom: 0;"><font color="#F5FFFA">Bob's Bike</font></h1>
       <input type="button" value="Edit Name"></div>     
        
         <div id="menu" style="background-color: #eeeff4;height: 600px;width: 200px;float: left;"></div> 
         <div id="menu1" style="background-color: #eeeff4;height: 600px;width: 200px;float: right;"></div> 
     </div>
    <body>
        
    <center>
        <h2>Customer Details:</h2>
    <table border='2'>
            <form action=" " method="POST">
           <tr> 
               <th>Billing Address</th>
               <th>Shipping Address</th>
               <th>Contact Information</th>
           </tr>
           <br>
           <tr>
               <td>Address Line1:</td>
                <td>Address Line1:</td>
                <td>Primary Phone:</td>
           </tr>
           
           <tr>
               <td><input type="text" name="Address"></td>
               <td><input type="text" name="Address"></td>
               <td><input type="text" name="Phone"></td>
            <br>
            </tr>
            <tr>
               <td>Address Line2:</td>
               <td>Address Line2:</td>
               <td>Fax:</td>
               
               
           </tr>
           <tr>
               <td><input type="text" name="Address"></td>
               <td><input type="text" name="SAddress"></td>
               <td><input type="text" name="Fax"></td>
            <br>
            </tr>
            <tr>
               <td>City:</td>
               <td>City:</td>
               <td>Email:</td>
               
           </tr>
           <tr>
               <td><input type="text" name="City"></td>
               <td><input type="text" name="City"></td>
               <td><input type="text" name="Email"></td>
            <br>
            </tr>
            <tr>
                <td>State</td>
                <td>State</td>
                <td>Web</td>
                </tr>
                <tr>
                <td><select>
                      
                        <option value="No">None</option>
                      <option value="CA">CA</option>
                      <option value="FL">FL</option>
                      <option value="NY">NY</option>
                    </select>
                </td>
                <td>
                <select>
                      
                        <option value="No">None</option>
                      <option value="CA">CA</option>
                      <option value="FL">FL</option>
                      <option value="NY">NY</option>
                    </select>
                </td>
                <td rowspan="1"><input type="text" name="Web"></td>
            </tr>
            <tr>
                <td>Zip</td>
                <td>Zip</td>
                
            </tr>
            <tr>
                <td><input type="text" name="Zip"></td>
                <td><input type="text" name="SZip"></td>
               
                
            </tr>
            <tr>
            <td><input type="button" value="Copy"></td>
            <td></td>
             <td><input type="button" value="Save Customer"></td>
            </tr>
            
           </div>
        </form>
      </table>
    </center>
       
    </body>
</html>
