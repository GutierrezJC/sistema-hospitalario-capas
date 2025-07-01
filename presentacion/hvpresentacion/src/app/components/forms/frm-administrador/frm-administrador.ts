import { Component, inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatDialog } from '@angular/material/dialog';
import { ReactiveFormsModule, FormBuilder, FormGroup, FormsModule } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { FrmVisitantes } from '../frm-visitantes/frm-visitantes';
import { AdministradorServices } from '../../../shared/services/administrador-services';
 //PARA HACER EL FORMULARIO RECATIVO ADEMAS OCUPO UN MODULO 
@Component({
  selector: 'app-frm-administrador',
 imports :[FormsModule,MatInputModule, MatButtonModule, MatIconModule, MatDialogModule, ReactiveFormsModule,],//PARA HACER EL FORMULARIO RECATIVO ADEMAS OCUPO UN MODULO 
  
  templateUrl: './frm-administrador.html',
  styleUrl: './frm-administrador.css'
})
export class FrmAdministrador {
  titulo!: string;
  Adminservices = inject(AdministradorServices);
  dialogRef = inject(MatDialogRef<FrmVisitantes>);
  private data = inject(MAT_DIALOG_DATA);// AQUI VIENEN LOS DATOS QUE SE PASAN AL FORMULARIO
  private readonly dialog = inject(MatDialog);//
  private builder = inject(FormBuilder);
  myForm: FormGroup;


  constructor() {
    this.myForm = this.builder.group({
      identificacion: ['', ],
      nombre: ['', ],
      apellido1: ['', ],
      apellido2: ['', ],
      telefono: ['', ],
      celular: ['', ],
      direccion: ['', ],
      correo: ['', ]
    });
  }

  onGuardar() {
    this.Adminservices.guardar(this.myForm.value)
      .subscribe({
        complete: () => {
          next: (data: any) => {
            // LO ESTOY DEJANDO LO MAS SENCILLO PARA QUE NO SE COMPLIQUE
            console.log('Visita guardada:', data);
            this.dialogRef.close();
            
            
          }
        },
        error: (err) => console.log(err)
      });
    this.dialogRef.close();
    
  }
}



