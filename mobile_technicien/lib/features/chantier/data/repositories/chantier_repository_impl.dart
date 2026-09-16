import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/chantier/data/datasources/chantier_remote_datasource.dart';
import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';
import 'package:mobile_technicien/features/chantier/domain/repositories/chantier_repository.dart';

class ChantierRepositoryImpl implements ChantierRepository {
  final ChantierRemoteDatasource remote;
  ChantierRepositoryImpl(this.remote);

  @override
  Future<Result<Chantier>> getChantierDetail(int chantierId) async {
    try {
      final chantier = await remote.getChantierDetail(chantierId);
      return Success(chantier);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
