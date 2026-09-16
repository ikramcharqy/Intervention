import 'package:equatable/equatable.dart';

/// Erreurs de domaine — jamais d'exception brute (Dio, etc.) au-delà de la
/// couche data, pour que le BLoC n'ait jamais à connaître Dio.
abstract class Failure extends Equatable {
  final String message;
  const Failure(this.message);

  @override
  List<Object?> get props => [message];
}

class ServerFailure extends Failure {
  const ServerFailure(super.message);
}

class NetworkFailure extends Failure {
  const NetworkFailure(super.message);
}

class UnauthorizedFailure extends Failure {
  const UnauthorizedFailure(super.message);
}

class CacheFailure extends Failure {
  const CacheFailure(super.message);
}
