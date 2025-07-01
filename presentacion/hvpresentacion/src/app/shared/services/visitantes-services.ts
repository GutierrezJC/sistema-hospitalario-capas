import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { inject } from '@angular/core';
import { TipoVisitante } from '../models/interfaces';
import { retry, map, catchError, throwError, of } from 'rxjs';

const _SERVER = "http://localhost:9000"; // URL del servidor

@Injectable({
  providedIn: 'root'
})
export class VisitantesServices {
  private readonly http = inject(HttpClient); // Inyección del servicio HttpClient

  constructor() { }
  filtrar(parametros: any) {
    let params = new HttpParams;
    for (const prop in parametros) {
      params = params.append(prop, parametros[prop]);
    }
    return this.http.get<any>(`${_SERVER}/api/visitante/filtrar/0/10`, { params: params });
  }


  guardar(datos: TipoVisitante, id?: number) {
    console.log(datos.id_Visitante + '  desde servicio id visitante');
    delete datos.id_Visitante; //elimina el id del objeto datos para que no se envíe al servidor, ya que el id se maneja de forma interna en la base de datos
    console.log(datos);
    var identificacion: string = datos.identificacion; // Asegurarse de que la identificación sea una cadena
    if (id) {
      // ruta de actualizar y quitar el idvisitante del objeto datos
      return this.http.put<any>(`${_SERVER}/api/visitante/${identificacion}`, datos);
    } else {
      // ruta de guardar y quitar el idvisitante del objeto datos
      return this.http.post<any>(`${_SERVER}/api/visitante`, datos);
    }
  }

  buscar(id: number) {
    return this.http.get<TipoVisitante>(`${_SERVER}/api/visitante/${id}`);
  }


  eliminar(id: string) {
    // any es para el cuerpo de la respuesta, que puede ser de cualquier tipo
    console.log(id + ' desde servicio id visitante');
    var identificacion: string = id; // Asegurarse de que la identificación sea una cadena
    return this.http.delete<any>(`${_SERVER}/api/visitante/${identificacion}`)
      .pipe(// buscar para que es pipe 
        retry(3), // reintentar 3 veces en caso de error
        map(() => true), // retornar true si la autenticación es exitosa
        catchError(this.handleRrror)

      );
  }

  resetearPassw(id: string) {
    console.log(id + ' id desde el servicio linea 16 ');
    return this.http.patch(`${_SERVER}/api/usuario/reset/${id}`, {}).pipe(
      map(() => true),
      catchError((error) => {
        return of(error.status);
      })
    );
  }

  private handleRrror(error: any) {
    return throwError(
      () => {
        return error.status
      }
    )
  }



}

