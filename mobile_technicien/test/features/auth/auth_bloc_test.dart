import 'package:bloc_test/bloc_test.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:mobile_technicien/core/error/failure.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/get_current_user_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/login_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/logout_usecase.dart';
import 'package:mobile_technicien/features/auth/presentation/bloc/auth_bloc.dart';

class MockAuthRepository extends Mock implements AuthRepository {}

void main() {
  late MockAuthRepository repository;
  late AuthBloc bloc;

  const user = Technicien(id: 1, name: 'Test', email: 't@t.ma', roles: ['technicien']);

  setUp(() {
    repository = MockAuthRepository();
    bloc = AuthBloc(
      loginUsecase: LoginUsecase(repository),
      getCurrentUserUsecase: GetCurrentUserUsecase(repository),
      logoutUsecase: LogoutUsecase(repository),
      repository: repository,
    );
  });

  tearDown(() => bloc.close());

  blocTest<AuthBloc, AuthState>(
    'emits [AuthLoading, AuthAuthenticated] on successful login',
    setUp: () {
      when(() => repository.login(email: any(named: 'email'), password: any(named: 'password')))
          .thenAnswer((_) async => const Success(user));
    },
    build: () => bloc,
    act: (b) => b.add(const AuthLoginRequested(email: 't@t.ma', password: 'secret')),
    expect: () => [const AuthLoading(), const AuthAuthenticated(user)],
  );

  blocTest<AuthBloc, AuthState>(
    'emits [AuthLoading, AuthUnauthenticated] with server message on bad credentials',
    setUp: () {
      when(() => repository.login(email: any(named: 'email'), password: any(named: 'password')))
          .thenAnswer((_) async => const Failed(UnauthorizedFailure('Identifiants incorrects.')));
    },
    build: () => bloc,
    act: (b) => b.add(const AuthLoginRequested(email: 't@t.ma', password: 'wrong')),
    expect: () => [const AuthLoading(), const AuthUnauthenticated(message: 'Identifiants incorrects.')],
  );
}
