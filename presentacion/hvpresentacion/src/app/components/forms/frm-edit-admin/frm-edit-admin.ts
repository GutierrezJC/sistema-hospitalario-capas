import { Component, inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef, MatDialog, MatDialogModule } from '@angular/material/dialog';
import { ReactiveFormsModule, FormBuilder, FormGroup } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { FrmVisitantes } from '../frm-visitantes/frm-visitantes';
import { AdministradorServices } from '../../../shared/services/administrador-services';
  //PARA HACER EL FORMULARIO RECATIVO ADEMAS OCUPO UN MODULO [ReactiveFormsModule, MatButtonModule, MatIconModule, MatDialogModule, ReactiveFormsModule,]


@Component({
  selector: 'app-frm-edit-admin',
  imports: [ReactiveFormsModule, MatButtonModule, MatIconModule, MatDialogModule],
  templateUrl: './frm-edit-admin.html',
  styleUrl: './frm-edit-admin.css'
})
export class FrmEditAdmin implements OnInit {
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
    // Por ahora solo usar guardar hasta que agregues el método actualizar al servicio
    this.Adminservices.editar(this.myForm.value)
      .subscribe({
        next: (data: any) => {
          console.log('Administrador guardado:', data);
          this.dialogRef.close(true); // Pasar true para indicar éxito
        },
        error: (err: any) => {
          console.log('Error al guardar:', err);
          this.dialogRef.close(false);
        }
      });
  }

   ngOnInit() {// Método que se ejecuta al inicializar el componente osea al iniiciar sea quei sean que lo llame 
    this.titulo = this.data.title;
    if (this.data.datos) {
      // Si hay datos (modo edición), llenar el formulario
      console.log('Datos recibidos: linea 60 en frm ', this.data.datos); // Para debug
      this.myForm.setValue({
        identificacion: this.data.datos.identificacion || '',
        nombre: this.data.datos.nombre || '',
        apellido1: this.data.datos.apellido1 || '',
        apellido2: this.data.datos.apellido2 || '',
        telefono: this.data.datos.telefono || '',
        celular: this.data.datos.celular || '',
        direccion: this.data.datos.direccion || '',
        correo: this.data.datos.correo || ''
        // Omitimos id_Administrador ya que no se usa en el formulario
      });
    }
  }
}



