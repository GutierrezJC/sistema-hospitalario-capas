import { Component, inject } from '@angular/core';
import { MatDialogModule, MatDialogRef } from '@angular/material/dialog';// EL MAT DIALOG RED ES PARA TENERE UNA INSTACIO UNA VEZ EL CUADRO CIERRA
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';// EL MAT BUTTON RED ES PARA TENERE UNA INSTACIO UNA VEZ EL CUADRO CIERRA
import { MatFormFieldModule } from '@angular/material/form-field';
import { ReactiveFormsModule } from '@angular/forms';// formularios reactivos 
import { FormBuilder, FormGroup } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatDividerModule } from '@angular/material/divider';
import { AuthService } from '../../../shared/services/auth-service';



@Component({
  selector: 'app-frm-login',
  imports: [MatDialogModule, MatIconModule, MatButtonModule, MatFormFieldModule, ReactiveFormsModule, MatInputModule, MatDividerModule],//TODO LO QUE ES MODULO SE TIENE QUE IMPORTAR
  templateUrl: './frm-login.html',
  styleUrl: './frm-login.css'
})
export class FrmLogin {
  readonly dialogRef = inject(MatDialogRef<FrmLogin>);// INYECTAR EL DIALOG REF PARA PODER CERRAR EL CUADRO DE DIALOGO
  // apartir de aqui yo tengo la referecia a el y puedo hacer algo a pesar que cierre
  // el cuadro de dialogo
  frmLogin: FormGroup;// declaro la variable de tipo FormGroup que es el formulario reactivo luego esa variable va estar depende con lo que declare con los fomControlsnam que estan en los imputs del html
  private srvAuth = inject(AuthService)
  private builder = inject(FormBuilder);// INYECTAR EL FORM BUILDER PARA PODER CREAR EL FORMULARIO REACTIVO
  private errorLogin : boolean =false
  constructor() {
    this.frmLogin = this.builder.group({// llamo al builder para crear el from y leugo lo guardo en la variable frmLogin
      id: (0), // CREAR EL CAMPO ID
      identificacion: [''],// CREAR EL CAMPO USUARIO
      passw: [''] // CREAR EL CAMPO CLAVE
    });
  }

  // el this retorna un observable que es lo que se va a retornar cuando se haga el login
  onlogin() {
    // value se retrona como un objeto entonces puedo elimiar atributos o campos que no necesito
    delete this.frmLogin.value.id; // ELIMINAR EL CAMPO ID DEL FORMULARIO
    console.log(this.frmLogin.value);// IMPRIMIR EL VALOR DEL FORMULARIO EN LA CONSOLA
    this.srvAuth.login(this.frmLogin.value)// EL SERVICO DE AUTENTICACION ESTAN AUTH 
      .subscribe((res) => { // SUSCRIBIRSE AL OBSERVABLE QUE RETORNA EL SERVICIO DE AUTENTICACION
        // aqui imppirmitalos los tokents que vienen en res 
        console.log('Login ESTE TRUE ES EL QUE VIENE DE MAP EN AUT LA LINEA 39 :', res); 
        this.errorLogin =!res || res === 401; // si el res es 401 o no hay res entonces errorLogin sera true
        if (!this.errorLogin) {
          this.dialogRef.close(); // CERRAR EL CUADRO DE DIALOGO SI EL LOGIN ES EXITOSO
        }
    
      })

  }


}
