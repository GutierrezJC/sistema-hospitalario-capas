import { Component, inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialog, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { VisitasServices } from '../../../shared/services/visitas-services';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { DialogoGeneral } from '../dialogo-general/dialogo-general';
import { AuthService } from '../../../shared/services/auth-service';


@Component({
  selector: 'app-frm-registro-visita',
  imports: [MatDialogModule, MatInputModule, MatButtonModule, MatIconModule, ReactiveFormsModule, MatFormFieldModule],
  templateUrl: './frm-registro-visita.html',
  styleUrl: './frm-registro-visita.css'
})
export class FrmRegistroVisita {
  titulo!: string;
  visitanteservice = inject(VisitasServices);
  dialogRef = inject(MatDialogRef<FrmRegistroVisita>);// Referencia al diálogo osea al formulario
  private data = inject(MAT_DIALOG_DATA);
  private readonly dialog = inject(MatDialog);
  private builder = inject(FormBuilder);
  myForm: FormGroup;

  public  srvAuth= inject(AuthService)



  constructor() {
    this.myForm = this.builder.group({
      identificacion_visitante: ['', ],
      nombre_visitante: ['', ],
      motivo_visita: ['', ],
      descripcion: ['']
    });
  }
  confirmaradmin(){
    if (this.srvAuth.userActualS().rol === '1') {
      return true; // El usuario es administrador
    }
    return false; // El usuario no es administrador
  }

  onguardar() {
    let identificacionAdmin: string;
    
    if (this.confirmaradmin() === false) {
      identificacionAdmin = '1234567';
    } else {
      identificacionAdmin = this.srvAuth.userActualS().id.toString();
    }
    
    const formData = this.myForm.value;
    const visitaParaGuardar = {
      identificacion_visitante: formData.identificacion_visitante,
      identificacion_administrador: identificacionAdmin, // Aquí usamos el ID determinado
      motivo_visita: formData.motivo_visita,
      fecha_entrada: new Date().toISOString().slice(0, 19).replace('T', ' '), // Fecha actual
      fecha_salida: null, // +1 hora
      estado: 'en curso'
    };

    this.visitanteservice.guardar(visitaParaGuardar)
      .subscribe({
        complete: () => {
          this.dialog.open(DialogoGeneral, {
            data: {
              texto: 'Visita creada correctamente',
              titulo: 'Visita creada',
              icono: 'check',
              textoAceptar: 'Aceptar'
            }
          });
          // Cierra el diálogo después de guardar
          this.dialogRef.close();
        }

      });
  }










}
