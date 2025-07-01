import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { catchError, map, of, pipe } from 'rxjs';

const _SERVER= 'http://localhost:9000';

@Injectable({
  providedIn: 'root'
})
export class UsuarioServices {
  private readonly http= inject(HttpClient);

  constructor() { }
resetearPassw(id: string) {
  console.log(id + ' id desde el servicio linea 16 ');
  return this.http.patch(`${_SERVER}/api/usuario/reset/${id}`, {}).pipe(
    map(() => true),
    catchError((error) => {
      return of(error.status);
    })
  );
}


}
