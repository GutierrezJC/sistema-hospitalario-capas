import { Component, inject } from '@angular/core';
import { VisitasServices } from '../../../shared/services/visitas-services';
import { MAT_DIALOG_DATA, MatDialog, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule } from '@angular/material/dialog';

@Component({
  selector: 'app-frm-cambio-estado',
  imports: [ReactiveFormsModule, MatDialogModule],// el dialogo no se puede importar en el  ademas es para los botones
  templateUrl: './frm-cambio-estado.html',
  styleUrl: './frm-cambio-estado.css'
})
export class FrmCambioEstado {
  titulo!: string;
  srvVisitante = inject(VisitasServices);
  dialogRef = inject(MatDialogRef<FrmCambioEstado>);
  private data = inject(MAT_DIALOG_DATA);
  private readonly dialog = inject(MatDialog);
  private builder = inject(FormBuilder);
  myForm: FormGroup;


  constructor() {
    this.myForm = this.builder.group({
      id_Visita: [],
      estado: []
    });
  }

  onGuardar() {

    const formData = this.myForm.value;
    const fechaActual = new Date().toISOString().slice(0, 19).replace('T', ' '); // Fecha actual

    console.log('Datos del formulario: actual  ', formData);
    console.log('Datos del formulario con el que se va a guardar: que vienen de bd  ', this.data.datos);

    // Toma los datos originales de la visita
    const datosOriginales = this.data.datos[0];

    // Crea el objeto actualizado, cambiando estado y fecha_salida
    const datosActualizados = {
      ...datosOriginales,
      estado: formData.estado,
      fecha_salida: fechaActual
    };

    // Llama al servicio para actualizar
    this.srvVisitante.editar(datosActualizados.id_visita, datosActualizados)
      .subscribe({
        next: () => this.dialogRef.close(true),
        error: () => this.dialogRef.close(false)
      });
  }

}
